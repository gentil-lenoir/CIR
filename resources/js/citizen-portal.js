const citizenConfig = () => {
    const body = document.body;

    return {
        page: body?.dataset.citizenPage || '',
        loginUrl: body?.dataset.loginUrl || '/login',
        apiMeUrl: body?.dataset.apiMeUrl || '/api/me',
        apiLogoutUrl: body?.dataset.apiLogoutUrl || '/api/logout',
        apiCitizenDashboardUrl: body?.dataset.apiCitizenDashboardUrl || '/api/citizen/dashboard',
        apiCitizenIssuesUrl: body?.dataset.apiCitizenIssuesUrl || '/api/citizen/issues',
        apiCitizenProfileUrl: body?.dataset.apiCitizenProfileUrl || '/api/citizen/profile',
        apiCitizenSettingsUrl: body?.dataset.apiCitizenSettingsUrl || '/api/citizen/settings',
        apiIssuesUrl: body?.dataset.apiIssuesUrl || '/api/issues',
        apiCreateIssueUrl: body?.dataset.apiCreateIssueUrl || '/api/issues',
        dashboardUrl: body?.dataset.dashboardUrl || '/citizen',
    };
};

const getToken = () => localStorage.getItem('token') || '';
const getStoredSettings = () => {
    try {
        return JSON.parse(localStorage.getItem('citizen_settings') || '{}');
    } catch {
        return {};
    }
};

const setStoredSettings = (settings) => {
    localStorage.setItem('citizen_settings', JSON.stringify(settings));
};

const setStoredUser = (user) => {
    localStorage.setItem('user', JSON.stringify(user));
};

const removeAuthState = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
};

const applyTheme = (settings = {}) => {
    const theme = settings.theme_preference || 'light';

    document.documentElement.dataset.citizenTheme = theme;
};

const apiRequest = async (url, options = {}) => {
    const headers = {
        Accept: 'application/json',
        ...(options.headers || {}),
    };

    if (!(options.body instanceof FormData) && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const token = getToken();

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(url, {
        ...options,
        headers,
    });

    const isJson = (response.headers.get('content-type') || '').includes('application/json');
    const payload = isJson ? await response.json() : {};

    if (!response.ok) {
        const error = new Error(payload.message || 'Request failed.');
        error.payload = payload;
        error.status = response.status;
        throw error;
    }

    return payload;
};

const requireCitizenAuth = async () => {
    const config = citizenConfig();

    if (!config.page) {
        return null;
    }

    const token = getToken();

    if (!token) {
        window.location.assign(config.loginUrl);
        return null;
    }

    try {
        const payload = await apiRequest(config.apiMeUrl, { method: 'GET' });
        const user = payload.user || payload.data?.user || null;

        if (!user || user.role !== 'citizen') {
            removeAuthState();
            window.location.assign(config.loginUrl);
            return null;
        }

        setStoredUser(user);

        return user;
    } catch {
        removeAuthState();
        window.location.assign(config.loginUrl);
        return null;
    }
};

const showMessage = (selector, message, type = 'info') => {
    const target = document.querySelector(selector);

    if (!target) {
        return;
    }

    if (!message) {
        target.className = 'hidden';
        target.textContent = '';
        return;
    }

    target.className =
        type === 'error'
            ? 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700'
            : 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700';
    target.textContent = message;
};

const formatStatus = (status) => status.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
const formatDate = (value) => (value ? new Date(value).toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' }) : 'Not set');
const formatDateTime = (value) => (value ? new Date(value).toLocaleString() : 'No updates yet');
const unwrapIssues = (payload) => payload.data?.data || payload.data || [];
const escapeHtml = (value = '') =>
    String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const statusClass = (status) => {
    switch (status) {
        case 'resolved':
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'in_progress':
            return 'bg-orange-100 text-orange-700 border-orange-200';
        default:
            return 'bg-blue-100 text-blue-700 border-blue-200';
    }
};

const loadSettings = async () => {
    const config = citizenConfig();

    try {
        const payload = await apiRequest(config.apiCitizenSettingsUrl, { method: 'GET' });
        const settings = payload.data?.settings || {};
        setStoredSettings(settings);
        applyTheme(settings);

        return settings;
    } catch {
        const settings = getStoredSettings();
        applyTheme(settings);
        return settings;
    }
};

const initShell = async (user) => {
    const frame = document.querySelector('[data-citizen-frame]');
    const nameNode = document.querySelector('[data-citizen-shell-name]');
    const emailNode = document.querySelector('[data-citizen-shell-email]');

    if (nameNode) {
        nameNode.textContent = user.name;
    }

    if (emailNode) {
        emailNode.textContent = user.email;
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'citizen_settings') {
            applyTheme(getStoredSettings());
        }
    });

    if (frame) {
        frame.addEventListener('load', () => {
            try {
                frame.contentWindow?.postMessage(
                    {
                        type: 'citizen-theme-sync',
                        payload: getStoredSettings(),
                    },
                    '*',
                );
            } catch {
                // Ignore frame sync errors.
            }
        });
    }

    document.querySelectorAll('[data-citizen-nav]').forEach((link) => {
        link.addEventListener('click', () => {
            document.getElementById('citizen-sidebar')?.classList.add('is-closed');
            document.querySelector('[data-drawer-backdrop="citizen-sidebar"]')?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    });
};

const initDashboardPage = async (user) => {
    const config = citizenConfig();
    const payload = await apiRequest(config.apiCitizenDashboardUrl, { method: 'GET' });
    const data = payload.data || {};
    const stats = data.stats || {};
    const recentIssues = data.recent_issues || [];

    document.querySelectorAll('[data-citizen-name]').forEach((node) => {
        node.textContent = user.name;
    });

    const mapping = {
        total_reported: stats.total_reported ?? 0,
        in_progress: stats.in_progress ?? 0,
        resolved: stats.resolved ?? 0,
        upvotes_received: stats.upvotes_received ?? 0,
        preferred_location: data.profile?.preferred_location || 'No preferred location set yet',
    };

    Object.entries(mapping).forEach(([key, value]) => {
        document.querySelectorAll(`[data-stat="${key}"]`).forEach((node) => {
            node.textContent = value;
        });
    });

    const recentIssuesNode = document.querySelector('[data-recent-issues]');

    if (recentIssuesNode) {
        recentIssuesNode.innerHTML = recentIssues.length
            ? recentIssues
                  .map(
                      (issue) => `
                <article class="p-6 flex items-start gap-6 hover:bg-navy-50/50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-navy-50 text-navy-700 flex items-center justify-center shrink-0 border border-navy-100">
                        <span class="material-symbols-outlined text-2xl">${issue.status === 'resolved' ? 'task_alt' : 'pending_actions'}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <h4 class="font-bold text-navy-900 text-lg">${escapeHtml(issue.title)}</h4>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border ${statusClass(issue.status)}">${escapeHtml(formatStatus(issue.status))}</span>
                        </div>
                        <p class="text-navy-600 text-sm mb-3">${escapeHtml(issue.address || 'Location not shared yet')}</p>
                        <div class="flex items-center gap-4 text-xs font-semibold text-navy-400">
                            <span>#CF-${issue.id}</span>
                            <span>${escapeHtml(formatDateTime(issue.reported_at))}</span>
                            <span>${issue.upvotes_count} upvotes</span>
                        </div>
                    </div>
                </article>`,
                  )
                  .join('')
            : '<div class="p-6 text-sm text-navy-500">No issues reported yet. Start with your first complaint.</div>';
    }
};

const initMyIssuesPage = async () => {
    const config = citizenConfig();
    const statusFilter = document.querySelector('[data-issues-status-filter]');
    const searchInput = document.querySelector('[data-issues-search]');
    const listNode = document.querySelector('[data-issues-list]');
    const totals = document.querySelectorAll('[data-issues-total]');

    const render = async () => {
        const searchParams = new URLSearchParams();

        if (statusFilter?.value) {
            searchParams.set('status', statusFilter.value);
        }

        if (searchInput?.value.trim()) {
            searchParams.set('search', searchInput.value.trim());
        }

        const payload = await apiRequest(`${config.apiCitizenIssuesUrl}?${searchParams.toString()}`, { method: 'GET' });
        const issues = unwrapIssues(payload);
        const total = payload.meta?.total ?? payload.data?.meta?.total ?? issues.length;

        totals.forEach((node) => {
            node.textContent = total;
        });

        listNode.innerHTML = issues.length
            ? issues
                  .map(
                      (issue) => `
                <div class="dashcode-surface p-6 sm:p-7">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 bg-navy-50 text-navy-700 border border-navy-100">
                                <span class="material-symbols-outlined text-2xl">${issue.status === 'resolved' ? 'task_alt' : 'pending_actions'}</span>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h2 class="text-xl font-bold text-navy-900">${escapeHtml(issue.title)}</h2>
                                    <span class="dashcode-pill ${statusClass(issue.status)}">${escapeHtml(formatStatus(issue.status))}</span>
                                </div>
                                <p class="text-sm text-navy-600 max-w-2xl">${escapeHtml(issue.description || 'No description')}</p>
                                <div class="mt-4 flex flex-wrap items-center gap-4 text-xs font-semibold text-navy-400">
                                    <span>#CF-${issue.id}</span>
                                    <span>${escapeHtml(issue.category || 'General')}</span>
                                    <span>${escapeHtml(formatDateTime(issue.reported_at))}</span>
                                    <span>${escapeHtml(issue.location?.address || 'Location pending')}</span>
                                    <span>${issue.worker?.name ? `Assigned: ${escapeHtml(issue.worker.name)}` : 'Not assigned yet'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`,
                  )
                  .join('')
            : '<div class="dashcode-surface p-6 text-sm text-navy-500">No issues matched your filter.</div>';
    };

    statusFilter?.addEventListener('change', render);
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchInput._citizenTimer);
        searchInput._citizenTimer = setTimeout(render, 250);
    });

    await render();
};

const initProfilePage = async () => {
    const config = citizenConfig();
    const dashboardPayload = await apiRequest(config.apiCitizenDashboardUrl, { method: 'GET' });
    const profile = dashboardPayload.data?.profile || {};
    const stats = dashboardPayload.data?.stats || {};
    const form = document.querySelector('[data-citizen-profile-form]');

    document.querySelectorAll('[data-profile-name]').forEach((node) => {
        node.textContent = profile.name || '';
    });
    document.querySelectorAll('[data-profile-email]').forEach((node) => {
        node.textContent = profile.email || '';
    });
    document.querySelectorAll('[data-profile-total]').forEach((node) => {
        node.textContent = stats.total_reported ?? 0;
    });
    document.querySelectorAll('[data-profile-resolved]').forEach((node) => {
        node.textContent = stats.resolved ?? 0;
    });

    if (!form) {
        return;
    }

    form.elements.name.value = profile.name || '';
    form.elements.email.value = profile.email || '';
    form.elements.phone.value = profile.phone || '';
    form.elements.preferred_location.value = profile.preferred_location || '';

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showMessage('[data-profile-message]', '');

        const formData = Object.fromEntries(new FormData(form).entries());

        try {
            await apiRequest(config.apiCitizenProfileUrl, {
                method: 'PUT',
                body: JSON.stringify(formData),
            });

            const me = await apiRequest(config.apiMeUrl, { method: 'GET' });
            setStoredUser(me.user || me.data?.user || {});
            showMessage('[data-profile-message]', 'Profile updated successfully.', 'success');
        } catch (error) {
            showMessage('[data-profile-message]', error.payload?.message || error.message, 'error');
        }
    });
};

const initSettingsPage = async () => {
    const config = citizenConfig();
    const form = document.querySelector('[data-citizen-settings-form]');
    const settingsPayload = await apiRequest(config.apiCitizenSettingsUrl, { method: 'GET' });
    const settings = settingsPayload.data?.settings || {};

    if (!form) {
        return;
    }

    form.elements.theme_preference.value = settings.theme_preference || 'light';
    form.addEventListener('change', () => {
        applyTheme({
            theme_preference: form.elements.theme_preference.value,
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            theme_preference: form.elements.theme_preference.value,
        };

        try {
            const response = await apiRequest(config.apiCitizenSettingsUrl, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });

            setStoredSettings(response.data?.settings || payload);
            applyTheme(response.data?.settings || payload);
            showMessage('[data-settings-message]', 'Settings saved successfully.', 'success');
        } catch (error) {
            showMessage('[data-settings-message]', error.payload?.message || error.message, 'error');
        }
    });
};

const createMap = (selector, options = {}) => {
    if (!window.L) {
        return null;
    }

    const node = document.querySelector(selector);

    if (!node) {
        return null;
    }

    const map = window.L.map(node).setView(options.center || [18.5204, 73.8567], options.zoom || 13);

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    return map;
};

const initMapPage = async () => {
    const config = citizenConfig();
    const map = createMap('[data-citizen-map]');
    const searchInput = document.querySelector('[data-map-search]');
    const statusFilter = document.querySelector('[data-map-status]');
    const resetButton = document.querySelector('[data-map-reset]');
    const listNode = document.querySelector('[data-map-list]');

    if (!map) {
        return;
    }

    let markers = [];

    const render = async () => {
        const payload = await apiRequest(config.apiIssuesUrl, { method: 'GET' });
        const issues = unwrapIssues(payload);
        const filtered = issues.filter((issue) => {
            const matchesStatus = !statusFilter?.value || issue.status === statusFilter.value;
            const term = searchInput?.value.trim().toLowerCase() || '';
            const haystack = `${issue.title} ${issue.location?.address || ''} ${issue.category || ''}`.toLowerCase();

            return matchesStatus && (!term || haystack.includes(term));
        });

        markers.forEach((marker) => marker.remove());
        markers = [];

        if (filtered.length) {
            const bounds = [];

            filtered.forEach((issue) => {
                const lat = issue.location?.latitude;
                const lng = issue.location?.longitude;

                if (typeof lat !== 'number' || typeof lng !== 'number') {
                    return;
                }

                const marker = window.L.marker([lat, lng]).addTo(map);
                marker.bindPopup(`<strong>${escapeHtml(issue.title)}</strong><br>${escapeHtml(formatStatus(issue.status))}<br>${escapeHtml(issue.location?.address || 'No address')}`);
                markers.push(marker);
                bounds.push([lat, lng]);
            });

            if (bounds.length) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        }

        if (listNode) {
            listNode.innerHTML = filtered.length
                ? filtered
                      .slice(0, 6)
                      .map(
                          (issue) => `
                    <article class="dashcode-surface p-4">
                        <p class="font-bold text-navy-900">${escapeHtml(issue.title)}</p>
                        <p class="mt-1 text-sm text-navy-500">${escapeHtml(issue.location?.address || 'No address')}</p>
                        <p class="mt-2 text-xs font-bold uppercase tracking-[0.18em] text-primary">${escapeHtml(formatStatus(issue.status))}</p>
                    </article>`,
                      )
                      .join('')
                : '<div class="dashcode-surface p-4 text-sm text-navy-500">No issues match the current filters.</div>';
        }
    };

    statusFilter?.addEventListener('change', render);
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchInput._mapTimer);
        searchInput._mapTimer = setTimeout(render, 250);
    });
    resetButton?.addEventListener('click', () => {
        if (searchInput) {
            searchInput.value = '';
        }
        if (statusFilter) {
            statusFilter.value = '';
        }
        render();
    });

    await render();
};

const initReportPage = async () => {
    const config = citizenConfig();
    const form = document.querySelector('[data-report-form]');
    const previewNode = document.querySelector('[data-upload-preview]');
    const map = createMap('[data-report-map]', { zoom: 15 });
    let marker = null;

    if (!form) {
        return;
    }

    const setCoordinates = (lat, lng) => {
        form.elements.latitude.value = lat.toFixed(7);
        form.elements.longitude.value = lng.toFixed(7);

        if (map) {
            if (marker) {
                marker.remove();
            }

            marker = window.L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 15);
        }
    };

    if (map) {
        map.on('click', (event) => {
            setCoordinates(event.latlng.lat, event.latlng.lng);
        });
    }

    document.querySelector('[data-use-location]')?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            showMessage('[data-report-message]', 'Geolocation is not available on this device.', 'error');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                setCoordinates(position.coords.latitude, position.coords.longitude);
                showMessage('[data-report-message]', 'Current location captured.', 'success');
            },
            () => {
                showMessage('[data-report-message]', 'Unable to fetch your current location.', 'error');
            },
        );
    });

    form.querySelector('input[name="images[]"]')?.addEventListener('change', (event) => {
        if (!previewNode) {
            return;
        }

        const files = Array.from(event.target.files || []);
        previewNode.innerHTML = files.length
            ? files
                  .map((file) => `<div class="rounded-2xl border border-navy-200 bg-white px-4 py-3 text-sm font-medium text-navy-600">${escapeHtml(file.name)}</div>`)
                  .join('')
            : '<p class="text-sm text-navy-400">No files selected yet.</p>';
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showMessage('[data-report-message]', '');

        const payload = new FormData(form);

        try {
            await apiRequest(config.apiCreateIssueUrl, {
                method: 'POST',
                body: payload,
            });

            form.reset();
            if (previewNode) {
                previewNode.innerHTML = '<p class="text-sm text-navy-400">No files selected yet.</p>';
            }
            showMessage('[data-report-message]', 'Issue reported successfully.', 'success');
        } catch (error) {
            showMessage('[data-report-message]', error.payload?.message || error.message, 'error');
        }
    });
};

const bindLogout = () => {
    document.querySelectorAll('[data-auth-logout]').forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();

            try {
                await apiRequest(citizenConfig().apiLogoutUrl, { method: 'POST' });
            } catch {
                // Ignore logout API failures on the client.
            }

            removeAuthState();
            window.location.assign(citizenConfig().loginUrl);
        });
    });
};

window.addEventListener('message', (event) => {
    if (event.data?.type === 'citizen-theme-sync') {
        applyTheme(event.data.payload || {});
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    const config = citizenConfig();

    applyTheme(getStoredSettings());

    if (!config.page) {
        return;
    }

    bindLogout();
    const user = await requireCitizenAuth();

    if (!user) {
        return;
    }

    await loadSettings();

    if (config.page === 'shell') {
        await initShell(user);
        return;
    }

    switch (config.page) {
        case 'dashboard':
            await initDashboardPage(user);
            break;
        case 'issues':
            await initMyIssuesPage();
            break;
        case 'profile':
            await initProfilePage();
            break;
        case 'settings':
            await initSettingsPage();
            break;
        case 'report':
            await initReportPage();
            break;
        case 'map':
            await initMapPage();
            break;
        default:
            break;
    }
});
