const TOKEN_KEY = 'token';
const USER_KEY = 'user';
const MIN_PASSWORD_LENGTH = 8;

function getToken() {
    return localStorage.getItem(TOKEN_KEY) || '';
}

function setToken(token) {
    if (!token) {
        return;
    }

    localStorage.setItem(TOKEN_KEY, token);
}

function removeToken() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem('jwt_token');
}

function getUser() {
    const rawUser = localStorage.getItem(USER_KEY) || localStorage.getItem('auth_user');

    if (!rawUser) {
        return null;
    }

    try {
        return JSON.parse(rawUser);
    } catch {
        return null;
    }
}

function setUser(user) {
    if (!user) {
        return;
    }

    localStorage.setItem(USER_KEY, JSON.stringify(user));
}

function removeUser() {
    localStorage.removeItem(USER_KEY);
    localStorage.removeItem('auth_user');
}

function isAuthenticated() {
    return Boolean(getToken());
}

function getConfig() {
    const body = document.body;

    return {
        page: body.dataset.authPage || '',
        apiLoginUrl: body.dataset.apiLoginUrl || '/api/login',
        apiRegisterUrl: body.dataset.apiRegisterUrl || '/api/register',
        apiLogoutUrl: body.dataset.apiLogoutUrl || '/api/logout',
        apiMeUrl: body.dataset.apiMeUrl || '/api/me',
        loginUrl: body.dataset.loginUrl || '/login',
        registerUrl: body.dataset.registerUrl || '/register',
        adminSessionLoginUrl: body.dataset.adminSessionLoginUrl || '/admin/session-login',
        dashboards: {
            citizen: body.dataset.dashboardCitizen || '/citizen',
            worker: body.dataset.dashboardWorker || '/worker',
            municipal_manager: body.dataset.dashboardMunicipalManager || '/admin',
            system_manager: body.dataset.dashboardSystemManager || '/admin',
            admin: body.dataset.dashboardMunicipalManager || '/admin',
            authority: body.dataset.dashboardMunicipalManager || '/admin',
        },
    };
}

function sanitizeValue(value) {
    return typeof value === 'string' ? value.trim() : value;
}

function sanitizeFormData(formData) {
    const cleaned = {};

    Object.entries(formData).forEach(([key, value]) => {
        cleaned[key] = key.toLowerCase().includes('password') ? value : sanitizeValue(value);
    });

    return cleaned;
}

function normalizeAuthResponse(payload) {
    return {
        token: payload?.access_token || payload?.data?.token || '',
        user: payload?.user || payload?.data?.user || null,
        message: payload?.message || '',
    };
}

async function parseJsonResponse(response) {
    const contentType = response.headers.get('content-type') || '';

    if (!contentType.includes('application/json')) {
        return {};
    }

    return response.json().catch(() => ({}));
}

async function apiRequest(url, options = {}) {
    const token = getToken();
    const headers = {
        Accept: 'application/json',
        ...(options.headers || {}),
    };

    if (!(options.body instanceof FormData) && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    let response;

    try {
        response = await fetch(url, {
            ...options,
            headers,
        });
    } catch {
        const error = new Error('Unable to reach the server. Please check your connection and try again.');
        error.status = 0;
        error.payload = {};
        throw error;
    }

    const payload = await parseJsonResponse(response);

    if (!response.ok) {
        const error = new Error(payload?.message || 'Request failed. Please try again.');
        error.status = response.status;
        error.payload = payload;
        throw error;
    }

    return payload;
}

function setAlert(message, type = 'error') {
    const alert = document.querySelector('[data-auth-alert]');

    if (!alert) {
        return;
    }

    if (!message) {
        alert.textContent = '';
        alert.className = 'auth-alert hidden';
        return;
    }

    alert.textContent = message;
    alert.className = `auth-alert auth-alert-${type}`;
}

function clearFieldErrors(form) {
    form.querySelectorAll('[data-field-error]').forEach((node) => {
        node.textContent = '';
    });

    form.querySelectorAll('.auth-input-error').forEach((node) => {
        node.classList.remove('auth-input-error');
        node.removeAttribute('aria-invalid');
    });
}

function setFieldError(form, field, message) {
    const input = form.querySelector(`[name="${field}"]`);
    const errorNode = form.querySelector(`[data-field-error="${field}"]`);

    if (input) {
        input.classList.add('auth-input-error');
        input.setAttribute('aria-invalid', 'true');
    }

    if (errorNode) {
        errorNode.textContent = message;
    }
}

function applyServerErrors(form, payload) {
    const errors = payload?.errors || {};
    const fields = Object.keys(errors);

    if (!fields.length) {
        return false;
    }

    fields.forEach((field) => {
        const messages = errors[field];
        setFieldError(form, field, Array.isArray(messages) ? messages[0] : String(messages));
    });

    return true;
}

function setSubmitting(form, isSubmitting) {
    const submitButton = form.querySelector('[type="submit"]');
    const spinner = form.querySelector('[data-submit-spinner]');
    const label = form.querySelector('[data-submit-label]');
    const loadingLabel = submitButton?.dataset.loadingLabel || 'Please wait...';
    const idleLabel = submitButton?.dataset.idleLabel || label?.textContent || '';

    if (submitButton) {
        submitButton.disabled = isSubmitting;
        submitButton.classList.toggle('is-loading', isSubmitting);
    }

    if (spinner) {
        spinner.classList.toggle('hidden', !isSubmitting);
    }

    if (label) {
        label.textContent = isSubmitting ? loadingLabel : idleLabel;
    }
}

function getRoleRedirect(role, config) {
    return config.dashboards[role] || config.dashboards.citizen;
}

function redirectToRoleDashboard(role, config = getConfig()) {
    window.location.assign(getRoleRedirect(role, config));
}

function collectFormData(form) {
    return sanitizeFormData(Object.fromEntries(new FormData(form).entries()));
}

function validateLoginForm(form) {
    const data = collectFormData(form);
    let isValid = true;

    if (!data.email) {
        setFieldError(form, 'email', 'Email is required.');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
        setFieldError(form, 'email', 'Enter a valid email address.');
        isValid = false;
    }

    if (!data.password) {
        setFieldError(form, 'password', 'Password is required.');
        isValid = false;
    }

    return { isValid, data };
}

function validateRegisterForm(form) {
    const data = collectFormData(form);
    data.role = 'citizen';
    let isValid = true;

    if (!data.name) {
        setFieldError(form, 'name', 'Name is required.');
        isValid = false;
    }

    if (!data.email) {
        setFieldError(form, 'email', 'Email is required.');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
        setFieldError(form, 'email', 'Enter a valid email address.');
        isValid = false;
    }

    if (!data.phone) {
        setFieldError(form, 'phone', 'Phone number is required.');
        isValid = false;
    } else if (!/^\d{10}$/.test(data.phone)) {
        setFieldError(form, 'phone', 'Phone number must be exactly 10 digits.');
        isValid = false;
    }

    if (!data.password) {
        setFieldError(form, 'password', 'Password is required.');
        isValid = false;
    } else if (data.password.length < MIN_PASSWORD_LENGTH) {
        setFieldError(form, 'password', `Password must be at least ${MIN_PASSWORD_LENGTH} characters.`);
        isValid = false;
    }

    if (!data.password_confirmation) {
        setFieldError(form, 'password_confirmation', 'Please confirm your password.');
        isValid = false;
    } else if (data.password !== data.password_confirmation) {
        setFieldError(form, 'password_confirmation', 'Passwords do not match.');
        isValid = false;
    }

    return { isValid, data };
}

async function syncAuthenticatedUser(config) {
    const token = getToken();

    if (!token) {
        return null;
    }

    try {
        const payload = await apiRequest(config.apiMeUrl, { method: 'GET' });
        const user = payload?.user || payload?.data?.user || getUser();

        if (user) {
            setUser(user);
        }

        return user;
    } catch (error) {
        if (error.status === 401) {
            removeToken();
            removeUser();
        }

        return null;
    }
}

async function submitLogin(form) {
    const config = getConfig();
    const { isValid, data } = validateLoginForm(form);

    if (!isValid) {
        setAlert('Please correct the highlighted fields.', 'error');
        return;
    }

    setSubmitting(form, true);

    try {
        const payload = await apiRequest(config.apiLoginUrl, {
            method: 'POST',
            body: JSON.stringify({
                email: data.email,
                password: data.password,
            }),
        });
        const auth = normalizeAuthResponse(payload);

        if (!auth.token || !auth.user) {
            throw new Error('Login succeeded but the server did not return usable authentication data.');
        }

        setToken(auth.token);
        setUser(auth.user);
        setAlert(auth.message || 'Login successful. Redirecting...', 'success');

        if (auth.user.role === 'municipal_manager') {
            await apiRequest(config.adminSessionLoginUrl, {
                method: 'POST',
                body: JSON.stringify({
                    email: data.email,
                    password: data.password,
                }),
            });
        }

        window.setTimeout(() => {
            redirectToRoleDashboard(auth.user.role, config);
        }, 500);
    } catch (error) {
        const handledFieldErrors = applyServerErrors(form, error.payload);
        const message =
            error.status === 422 && !handledFieldErrors
                ? error.payload?.message || 'Invalid credentials. Please try again.'
                : error.message;

        setAlert(message, 'error');
    } finally {
        setSubmitting(form, false);
    }
}

async function submitRegister(form) {
    const config = getConfig();
    const { isValid, data } = validateRegisterForm(form);

    if (!isValid) {
        setAlert('Please correct the highlighted fields.', 'error');
        return;
    }

    setSubmitting(form, true);

    try {
        const payload = await apiRequest(config.apiRegisterUrl, {
            method: 'POST',
            body: JSON.stringify({
                name: data.name,
                email: data.email,
                phone: data.phone,
                password: data.password,
                password_confirmation: data.password_confirmation,
                role: 'citizen',
            }),
        });

        removeToken();
        removeUser();
        setAlert(payload?.message || 'Registration successful. Redirecting to login...', 'success');
        form.reset();

        window.setTimeout(() => {
            window.location.assign(config.loginUrl);
        }, 1000);
    } catch (error) {
        applyServerErrors(form, error.payload);
        setAlert(error.payload?.message || error.message, 'error');
    } finally {
        setSubmitting(form, false);
    }
}

function bindAuthForms() {
    const loginForm = document.querySelector('[data-auth-form="login"]');
    const registerForm = document.querySelector('[data-auth-form="register"]');

    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFieldErrors(loginForm);
            setAlert('');
            await submitLogin(loginForm);
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFieldErrors(registerForm);
            setAlert('');
            await submitRegister(registerForm);
        });
    }
}

function bindPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.passwordToggle;
            const input = document.getElementById(targetId);
            const icon = button.querySelector('.material-symbols-outlined');

            if (!input) {
                return;
            }

            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';

            if (icon) {
                icon.textContent = showPassword ? 'visibility_off' : 'visibility';
            }
        });
    });
}

async function logout(redirectUrl = null) {
    const config = getConfig();

    try {
        if (getToken()) {
            await apiRequest(config.apiLogoutUrl, { method: 'POST' });
        }
    } catch {
        // Clear local auth state even if the server cannot be reached.
    } finally {
        removeToken();
        removeUser();
        window.location.assign(redirectUrl || config.loginUrl);
    }
}

function bindLogoutButtons() {
    document.querySelectorAll('[data-auth-logout]').forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            await logout(button.dataset.redirectUrl || null);
        });
    });
}

function bindFieldCleanup() {
    document.querySelectorAll('input, select').forEach((field) => {
        if (field.name === 'phone') {
            field.addEventListener('input', () => {
                field.value = field.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        field.addEventListener('input', () => {
            field.classList.remove('auth-input-error');
            field.removeAttribute('aria-invalid');

            const errorNode = document.querySelector(`[data-field-error="${field.name}"]`);

            if (errorNode) {
                errorNode.textContent = '';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    bindPasswordToggles();
    bindLogoutButtons();
    bindFieldCleanup();
    bindAuthForms();
});

window.Auth = {
    getToken,
    setToken,
    removeToken,
    getUser,
    isAuthenticated,
    logout,
};

export { getToken, setToken, removeToken, getUser, isAuthenticated, logout };
