<!DOCTYPE html>
<html class="light" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Login | Smart Public Issue Reporting</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />

        @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/auth.js'])
        
        <style>
            body {
                background: #f8fafc;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            
            .auth-form-card {
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 2.5rem;
                width: 100%;
                max-width: 420px;
            }
            
            .auth-logo {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 0.25rem;
            }
            
            .auth-logo-tag {
                font-size: 0.875rem;
                color: #64748b;
                margin-bottom: 2rem;
            }
            
            .auth-alert {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #dc2626;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                margin-bottom: 1.5rem;
                font-size: 0.875rem;
            }
            
            .dashcode-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                color: #334155;
                margin-bottom: 0.5rem;
            }
            
            .dashcode-input {
                width: 100%;
                padding: 0.625rem 0.875rem;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                font-size: 0.9375rem;
                outline: none;
            }
            
            .dashcode-input:focus {
                border-color: #2563eb;
            }
            
            .dashcode-input::placeholder {
                color: #94a3b8;
            }
            
            .dashcode-btn-primary {
                background: #2563eb;
                color: white;
                padding: 0.75rem;
                border-radius: 6px;
                font-weight: 500;
                font-size: 0.9375rem;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }
            
            .dashcode-btn-primary:hover {
                background: #1d4ed8;
            }
            
            .auth-icon-button {
                position: absolute;
                right: 0.5rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                padding: 0.25rem;
                color: #94a3b8;
            }
            
            .auth-field-error {
                color: #dc2626;
                font-size: 0.8125rem;
                margin-top: 0.25rem;
                display: none;
            }
            
            .auth-footer-copy {
                text-align: center;
                margin-top: 1.5rem;
                padding-top: 1.5rem;
                border-top: 1px solid #e2e8f0;
            }
            
            .auth-footer-copy a {
                color: #2563eb;
                text-decoration: none;
                font-weight: 500;
            }
            
            .auth-footer-copy a:hover {
                text-decoration: underline;
            }
            
            .auth-spinner {
                width: 18px;
                height: 18px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 0.6s linear infinite;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>
    </head>

    <body
        class="dashcode-page auth-shell"
        data-api-login-url="{{ url('/api/login') }}"
        data-api-logout-url="{{ url('/api/logout') }}"
        data-api-me-url="{{ url('/api/me') }}"
        data-auth-page="login"
        data-dashboard-municipal-manager="{{ route('admin.dashboard') }}"
        data-dashboard-system-manager="{{ route('admin.dashboard') }}"
        data-admin-session-login-url="{{ route('admin.session-login') }}"
        data-dashboard-citizen="{{ route('citizen.dashboard') }}"
        data-dashboard-worker="{{ route('worker.dashboard') }}"
        data-login-url="{{ route('login') }}"
        data-register-url="{{ route('register') }}"
    >
        <div class="auth-form-card">
            <div class="auth-logo">CIR</div>
            <p class="auth-logo-tag">Smart Public Issue Reporting</p>

            <div class="auth-alert hidden" data-auth-alert></div>

            <form style="display: flex; flex-direction: column; gap: 1.25rem;" data-auth-form="login" novalidate>
                <div>
                    <label class="dashcode-label" for="email">Email Address</label>
                    <input autocomplete="email" class="dashcode-input" id="email" name="email" placeholder="alex@example.com" type="email" />
                    <p class="auth-field-error" data-field-error="email"></p>
                </div>

                <div>
                    <label class="dashcode-label" for="password">Password</label>
                    <div style="position: relative;">
                        <input
                            autocomplete="current-password"
                            class="dashcode-input"
                            style="padding-right: 2.5rem;"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            type="password"
                        />
                        <button
                            aria-label="Toggle password visibility"
                            class="auth-icon-button"
                            data-password-toggle="password"
                            type="button"
                        >
                            <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                        </button>
                    </div>
                    <p class="auth-field-error" data-field-error="password"></p>
                </div>

                <button
                    class="dashcode-btn-primary"
                    style="width: 100%; margin-top: 0.5rem;"
                    data-idle-label="Login to Account"
                    data-loading-label="Signing in..."
                    type="submit"
                >
                    <span data-submit-label>Login to Account</span>
                    <span class="auth-spinner hidden" data-submit-spinner></span>
                </button>
            </form>

            <div class="auth-footer-copy">
                <p style="font-size: 0.875rem; color: #64748b;">
                    Need a new account?
                    <a href="{{ route('register') }}">Create one here</a>
                </p>
            </div>
        </div>
    </body>
</html>