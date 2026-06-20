const workerConfig = () => {
    const body = document.body;
    return {
        page: body?.dataset.workerPage || '',
        loginUrl: body?.dataset.loginUrl || '/login',
        dashboardUrl: body?.dataset.dashboardUrl || '/worker',
        apiMeUrl: body?.dataset.apiMeUrl || '/api/me',
        apiLogoutUrl: body?.dataset.apiLogoutUrl || '/api/logout',
        apiWorkerDashboardUrl: body?.dataset.apiWorkerDashboardUrl || '/api/worker/dashboard',
        apiWorkerIssuesUrl: body?.dataset.apiWorkerIssuesUrl || '/api/worker/issues',
        apiWorkerIssueStatusUrlTemplate: body?.dataset.apiWorkerIssueStatusUrlTemplate || '/api/worker/issues/__ISSUE__/status',
        apiWorkerProfileUrl: body?.dataset.apiWorkerProfileUrl || '/api/worker/profile',
        apiWorkerSettingsUrl: body?.dataset.apiWorkerSettingsUrl || '/api/worker/settings',
        apiWorkerPerformanceUrl: body?.dataset.apiWorkerPerformanceUrl || '/api/worker/performance',
    };
};

const getToken = () => localStorage.getItem('token') || '';
const getSettings = () => { try { return JSON.parse(localStorage.getItem('worker_settings') || '{}'); } catch { return {}; } };
const setSettings = (settings) => localStorage.setItem('worker_settings', JSON.stringify(settings));
const clearAuth = () => { localStorage.removeItem('token'); localStorage.removeItem('user'); };
const applyTheme = (settings = {}) => { document.documentElement.dataset.workerTheme = settings.theme_preference || 'light'; };
const fmtStatus = (value = '') => value.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
const fmtDate = (value) => (value ? new Date(value).toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' }) : 'No deadline');
const fmtDateTime = (value) => (value ? new Date(value).toLocaleString() : 'Not available');
const escapeHtml = (value = '') => String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');

const apiRequest = async (url, options = {}) => {
    const headers = { Accept: 'application/json', ...(options.headers || {}) };
    if (!(options.body instanceof FormData) && !headers['Content-Type']) headers['Content-Type'] = 'application/json';
    if (getToken()) headers.Authorization = `Bearer ${getToken()}`;
    const response = await fetch(url, { ...options, headers });
    const payload = (response.headers.get('content-type') || '').includes('application/json') ? await response.json().catch(() => ({})) : {};
    if (!response.ok) {
        const error = new Error(payload?.message || 'Worker request failed.');
        error.status = response.status;
        error.payload = payload;
        throw error;
    }
    return payload;
};

const showAlert = (target, message, tone = 'error') => {
    if (!target) return;
    if (!message) { target.className = 'hidden'; target.textContent = ''; return; }
    target.className = tone === 'success'
        ? 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700'
        : 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700';
    target.textContent = message;
};

const requireWorker = async () => {
    if (!getToken()) { window.location.assign(workerConfig().loginUrl); return null; }
    try {
        const payload = await apiRequest(workerConfig().apiMeUrl, { method: 'GET' });
        const user = payload.user || payload.data?.user || null;
        if (!user || user.role !== 'worker') throw new Error('Invalid worker session.');
        localStorage.setItem('user', JSON.stringify(user));
        return user;
    } catch {
        clearAuth();
        window.location.assign(workerConfig().loginUrl);
        return null;
    }
};

const loadSettings = async () => {
    try {
        const payload = await apiRequest(workerConfig().apiWorkerSettingsUrl, { method: 'GET' });
        const settings = payload.data?.settings || {};
        setSettings(settings);
        applyTheme(settings);
        return settings;
    } catch {
        const settings = getSettings();
        applyTheme(settings);
        return settings;
    }
};

const issueStatusClass = (status) => status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700';
const priorityClass = (priority) => ['urgent', 'high'].includes(priority) ? 'bg-red-100 text-red-700' : priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700';

const loadDashboard = async () => (await apiRequest(workerConfig().apiWorkerDashboardUrl, { method: 'GET' })).data || {};
const loadIssues = async (view, search = '') => {
    const params = new URLSearchParams({ view });
    if (search.trim()) params.set('search', search.trim());
    return (await apiRequest(`${workerConfig().apiWorkerIssuesUrl}?${params.toString()}`, { method: 'GET' })).data || {};
};
const updateIssue = async (issueId, formData) => apiRequest(workerConfig().apiWorkerIssueStatusUrlTemplate.replace('__ISSUE__', issueId), { method: 'POST', body: formData });

const initShell = (user) => {
    document.querySelectorAll('[data-worker-shell-name]').forEach((node) => node.textContent = user.name || 'Worker');
    document.querySelectorAll('[data-worker-shell-email]').forEach((node) => node.textContent = user.email || 'worker@example.com');
    document.querySelectorAll('[data-worker-shell-department]').forEach((node) => node.textContent = user.department?.name || 'Department pending');
    document.querySelectorAll('[data-worker-nav]').forEach((link) => link.addEventListener('click', () => {
        document.querySelectorAll('[data-worker-nav]').forEach((item) => item.classList.remove('active'));
        link.classList.add('active');
        document.getElementById('worker-sidebar')?.classList.add('is-closed');
        document.querySelector('[data-drawer-backdrop="worker-sidebar"]')?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }));
    const frame = document.querySelector('[data-worker-frame]');
    const syncTheme = () => frame?.contentWindow?.postMessage({ type: 'worker-theme-sync', payload: getSettings() }, '*');
    frame?.addEventListener('load', syncTheme);
    window.addEventListener('storage', (event) => { if (event.key === 'worker_settings') { applyTheme(getSettings()); syncTheme(); } });
    syncTheme();
};

const initOverview = async () => {
    const data = await loadDashboard();
    const stats = data.stats || {};
    const profile = data.profile || {};
    document.querySelector('[data-worker-overview-status]')?.replaceChildren(document.createTextNode(fmtStatus(profile.availability_status || 'available')));
    document.querySelector('[data-worker-overview-department]')?.replaceChildren(document.createTextNode(profile.department || 'Department pending'));
    Object.entries(stats).forEach(([key, value]) => document.querySelectorAll(`[data-worker-stat="${key}"]`).forEach((node) => node.textContent = String(value ?? 0)));
    const root = document.querySelector('[data-worker-recent-assignments]');
    if (!root) return;
    const items = data.recent_assignments || [];
    root.innerHTML = items.length ? items.map((issue) => `
        <article class="worker-task-card p-5">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-sm font-bold text-slate-900">${escapeHtml(issue.title)}</p><p class="text-sm text-slate-500 mt-1">${escapeHtml(issue.location?.address || 'Location pending')}</p></div>
                <span class="worker-priority-chip ${priorityClass(issue.priority)}">${escapeHtml(fmtStatus(issue.priority || 'low'))}</span>
            </div>
            <p class="text-sm text-slate-600 mt-4">${escapeHtml(issue.description || 'No description shared yet.')}</p>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500"><span>${escapeHtml(fmtStatus(issue.status))}</span><span>${escapeHtml(fmtDate(issue.deadline))}</span><span>${issue.is_overdue ? 'Overdue' : issue.is_due_today ? 'Due today' : 'On track'}</span></div>
        </article>`).join('') : '<article class="worker-task-card p-5"><p class="text-sm text-slate-500">No assignments are currently available.</p></article>';
};

const initAssigned = async () => {
    const alert = document.querySelector('[data-worker-issues-alert]');
    const search = document.querySelector('[data-worker-search]');
    const table = document.querySelector('[data-worker-assigned-table]');
    const render = async () => {
        try {
            showAlert(alert, '');
            const data = await loadIssues('assigned', search?.value || '');
            const issues = data.issues || [];
            document.querySelectorAll('[data-worker-count="assigned"]').forEach((node) => node.textContent = String(data.counts?.assigned ?? 0));
            table.innerHTML = issues.length ? issues.map((issue) => `
                <tr>
                    <td class="worker-table-cell"><p class="font-bold text-slate-900">${escapeHtml(issue.title)}</p><p class="text-xs text-slate-500 mt-1">${escapeHtml(issue.location?.address || issue.category || 'No location')}</p></td>
                    <td class="worker-table-cell"><span class="worker-priority-chip ${priorityClass(issue.priority)}">${escapeHtml(fmtStatus(issue.priority || 'low'))}</span></td>
                    <td class="worker-table-cell ${issue.is_overdue ? 'text-red-600 font-bold' : ''}">${escapeHtml(fmtDate(issue.deadline))}</td>
                    <td class="worker-table-cell"><span class="worker-status-badge ${issueStatusClass(issue.status)}">${escapeHtml(fmtStatus(issue.status))}</span></td>
                    <td class="worker-table-cell"><div class="flex flex-wrap gap-2">
                        ${issue.status !== 'in_progress' ? `<button class="worker-action-btn border-emerald-200 bg-emerald-50 text-emerald-700" data-worker-quick-start="${issue.id}" type="button">Start Work</button>` : `<a class="worker-action-btn border-slate-200 bg-white text-slate-700" href="${workerConfig().dashboardUrl}/in-progress">Continue</a>`}
                        ${issue.maps_url ? `<a class="worker-action-btn border-slate-200 bg-white text-slate-700" href="${issue.maps_url}" target="_blank" rel="noopener noreferrer">Open Map</a>` : ''}
                    </div></td>
                </tr>`).join('') : '<tr><td class="worker-table-cell text-slate-500" colspan="5">No assigned issues matched your search.</td></tr>';
            table.querySelectorAll('[data-worker-quick-start]').forEach((button) => button.addEventListener('click', async () => {
                const formData = new FormData();
                formData.append('status', 'in_progress');
                try { await updateIssue(button.dataset.workerQuickStart, formData); await render(); } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
            }));
        } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    };
    search?.addEventListener('input', () => { clearTimeout(search._t); search._t = setTimeout(render, 250); });
    await render();
};

const initInProgress = async () => {
    const alert = document.querySelector('[data-worker-in-progress-alert]');
    const list = document.querySelector('[data-worker-in-progress-list]');
    const render = async () => {
        try {
            showAlert(alert, '');
            const data = await loadIssues('in_progress');
            document.querySelectorAll('[data-worker-count="in_progress"]').forEach((node) => node.textContent = String(data.counts?.in_progress ?? 0));
            const issues = data.issues || [];
            list.innerHTML = issues.length ? issues.map((issue) => `
                <article class="worker-task-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div><h2 class="text-xl font-bold text-slate-900">${escapeHtml(issue.title)}</h2><p class="text-sm text-slate-500 mt-2">${escapeHtml(issue.location?.address || 'Location pending')} • ${escapeHtml(issue.category || 'General')}</p></div>
                        <span class="worker-status-badge ${issueStatusClass(issue.status)}">${escapeHtml(fmtStatus(issue.status))}</span>
                    </div>
                    <p class="text-sm text-slate-600 mt-4">${escapeHtml(issue.description || 'No description provided.')}</p>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs font-semibold ${issue.is_overdue ? 'text-red-600' : 'text-slate-500'}"><span>${escapeHtml(fmtDate(issue.deadline))}</span><span>${issue.is_overdue ? 'Overdue' : issue.is_due_today ? 'Due today' : 'On track'}</span><span>${escapeHtml(fmtDateTime(issue.updated_at))}</span></div>
                    <form class="mt-6 space-y-4" data-worker-progress-form="${issue.id}">
                        <div><label class="worker-form-label" for="notes-${issue.id}">Progress Notes</label><textarea class="worker-form-input min-h-28" id="notes-${issue.id}" name="notes" placeholder="Add work completed, blockers, or closure notes."></textarea></div>
                        <div><label class="worker-form-label" for="proof-${issue.id}">Closure Image</label><input class="worker-form-input" id="proof-${issue.id}" name="proof_image" type="file" accept="image/*" /></div>
                        <div class="flex flex-wrap gap-3">
                            <button class="worker-action-btn border-emerald-200 bg-emerald-50 text-emerald-700" data-worker-progress-action="in_progress" type="submit">Save Progress</button>
                            <button class="worker-action-btn border-primary/20 bg-primary text-white" data-worker-progress-action="resolved" type="submit">Mark Resolved</button>
                            ${issue.maps_url ? `<a class="worker-action-btn border-slate-200 bg-white text-slate-700" href="${issue.maps_url}" target="_blank" rel="noopener noreferrer">Open Map</a>` : ''}
                        </div>
                    </form>
                </article>`).join('') : '<article class="worker-task-card p-6"><p class="text-sm text-slate-500">No issues are currently in progress.</p></article>';
            list.querySelectorAll('[data-worker-progress-form]').forEach((form) => form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const formData = new FormData(form);
                formData.set('status', event.submitter?.dataset.workerProgressAction || 'in_progress');
                try {
                    await updateIssue(form.dataset.workerProgressForm, formData);
                    showAlert(alert, formData.get('status') === 'resolved' ? 'Issue marked resolved successfully.' : 'Progress saved successfully.', 'success');
                    await render();
                } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
            }));
        } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    };
    await render();
};

const initResolved = async () => {
    const list = document.querySelector('[data-worker-resolved-list]');
    const data = await loadIssues('resolved');
    document.querySelectorAll('[data-worker-count="resolved"]').forEach((node) => node.textContent = String(data.counts?.resolved ?? 0));
    const issues = data.issues || [];
    list.innerHTML = issues.length ? issues.map((issue) => {
        const image = issue.images?.[issue.images.length - 1]?.url || '';
        return `<article class="worker-chart-container">
            <div class="flex items-start justify-between gap-4"><div><h2 class="text-xl font-extrabold text-slate-900">${escapeHtml(issue.title)}</h2><p class="mt-2 text-sm text-slate-500">${escapeHtml(issue.location?.address || 'Location pending')}</p></div><span class="worker-status-badge bg-emerald-100 text-emerald-700">Resolved</span></div>
            <p class="mt-4 text-sm text-slate-600">${escapeHtml(issue.description || 'No description provided.')}</p>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500"><span>${escapeHtml(fmtDateTime(issue.updated_at))}</span><span>${escapeHtml(fmtStatus(issue.priority || 'low'))} priority</span></div>
            ${image ? `<img alt="Issue proof" class="mt-5 h-48 w-full rounded-2xl object-cover border border-slate-200" src="${image}" />` : `<div class="mt-5 rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">No closure image uploaded for this issue.</div>`}
        </article>`;
    }).join('') : '<article class="worker-chart-container"><p class="text-sm text-slate-500">No resolved issues are available yet.</p></article>';
};

const initProfile = async () => {
    const form = document.querySelector('[data-worker-profile-form]');
    if (!form) return;
    const alert = document.querySelector('[data-worker-profile-alert]');
    try {
        const payload = await apiRequest(workerConfig().apiWorkerProfileUrl);
        const profile = payload.data?.profile || {};
        form.elements.name.value = profile.name || '';
        form.elements.email.value = profile.email || '';
        form.elements.phone.value = profile.phone || '';
        form.elements.availability_status.value = profile.availability_status || 'available';
        const mappings = {
            '[data-worker-profile-initials]': profile.initials || 'WK',
            '[data-worker-profile-name]': profile.name || 'Worker',
            '[data-worker-profile-role]': profile.role_label || 'Municipal Field Worker',
            '[data-worker-profile-code]': profile.worker_code || 'WK-0000',
            '[data-worker-profile-email]': profile.email || 'Not added',
            '[data-worker-profile-phone]': profile.phone || 'Not added',
            '[data-worker-profile-status]': fmtStatus(profile.availability_status || 'available'),
            '[data-worker-profile-department]': profile.department || 'Not assigned',
            '[data-worker-profile-account-status]': fmtStatus(profile.account_status || 'active'),
        };
        Object.entries(mappings).forEach(([selector, value]) => document.querySelectorAll(selector).forEach((node) => node.textContent = value));
    } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
            const payload = { name: form.elements.name.value.trim(), email: form.elements.email.value.trim(), phone: form.elements.phone.value.trim(), availability_status: form.elements.availability_status.value };
            const response = await apiRequest(workerConfig().apiWorkerProfileUrl, { method: 'PUT', body: JSON.stringify(payload) });
            const profile = response.data?.profile || {};
            document.querySelectorAll('[data-worker-profile-name]').forEach((node) => node.textContent = profile.name || 'Worker');
            document.querySelectorAll('[data-worker-profile-email]').forEach((node) => node.textContent = profile.email || 'Not added');
            document.querySelectorAll('[data-worker-profile-phone]').forEach((node) => node.textContent = profile.phone || 'Not added');
            document.querySelectorAll('[data-worker-profile-status]').forEach((node) => node.textContent = fmtStatus(profile.availability_status || 'available'));
            showAlert(alert, response.message || 'Profile updated successfully.', 'success');
        } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    });
};

const initSettingsPage = async () => {
    const form = document.querySelector('[data-worker-settings-form]');
    if (!form) return;
    const alert = document.querySelector('[data-worker-settings-alert]');
    try {
        const payload = await apiRequest(workerConfig().apiWorkerSettingsUrl);
        const settings = payload.data?.settings || {};
        form.elements.theme_preference.value = settings.theme_preference || 'light';
        form.elements.preferred_zone.value = settings.preferred_zone || '';
        form.elements.shift_window.value = settings.shift_window || '';
    } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    form.addEventListener('change', () => applyTheme({ theme_preference: form.elements.theme_preference.value }));
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
            const payload = {
                theme_preference: form.elements.theme_preference.value,
                preferred_zone: form.elements.preferred_zone.value.trim(),
                shift_window: form.elements.shift_window.value.trim(),
            };
            const response = await apiRequest(workerConfig().apiWorkerSettingsUrl, { method: 'PUT', body: JSON.stringify(payload) });
            const settings = response.data?.settings || payload;
            setSettings(settings);
            applyTheme(settings);
            showAlert(alert, response.message || 'Settings saved successfully.', 'success');
        } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
    });
};

const initPerformance = async () => {
    if (!document.querySelector('[data-worker-performance]')) return;
    const alert = document.querySelector('[data-worker-performance-alert]');
    try {
        const payload = await apiRequest(workerConfig().apiWorkerPerformanceUrl);
        const summary = payload.data?.performance?.summary || {};
        const recentResolved = payload.data?.performance?.recent_resolved || [];
        const values = {
            '[data-performance-total-assigned]': summary.total_assigned ?? 0,
            '[data-performance-resolved]': summary.resolved_count ?? 0,
            '[data-performance-completion-rate]': `${summary.completion_rate ?? 0}%`,
            '[data-performance-average-resolution]': `${summary.average_resolution_hours ?? 0}h`,
            '[data-performance-assigned]': summary.assigned_count ?? 0,
            '[data-performance-in-progress]': summary.in_progress_count ?? 0,
            '[data-performance-high-priority]': summary.high_priority_open_count ?? 0,
            '[data-performance-started-fast]': summary.started_within_day_count ?? 0,
        };
        Object.entries(values).forEach(([selector, value]) => document.querySelector(selector)?.replaceChildren(document.createTextNode(String(value))));
        const list = document.querySelector('[data-performance-recent-resolved]');
        if (list) list.innerHTML = recentResolved.length ? recentResolved.map((item) => `
            <li class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <div class="flex items-start justify-between gap-4"><div><h3 class="text-base font-bold text-slate-900">${escapeHtml(item.title || 'Issue')}</h3><p class="mt-1 text-sm text-slate-500">Priority ${escapeHtml(fmtStatus(item.priority || 'normal'))}</p></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Resolved</span></div>
                <p class="mt-3 text-sm text-slate-500">Closed ${escapeHtml(fmtDateTime(item.resolved_at))}</p>
            </li>`).join('') : '<li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">Resolved issues will appear here once your assignments are closed.</li>';
    } catch (error) { showAlert(alert, error.payload?.message || error.message, 'error'); }
};

const bindLogout = () => {
    document.querySelectorAll('[data-auth-logout]').forEach((button) => button.addEventListener('click', async (event) => {
        event.preventDefault();
        try { await apiRequest(workerConfig().apiLogoutUrl, { method: 'POST' }); } catch {}
        clearAuth();
        window.location.assign(workerConfig().loginUrl);
    }));
};

window.addEventListener('message', (event) => { if (event.data?.type === 'worker-theme-sync') applyTheme(event.data.payload || {}); });

document.addEventListener('DOMContentLoaded', async () => {
    applyTheme(getSettings());
    if (!workerConfig().page) return;
    bindLogout();
    const user = await requireWorker();
    if (!user) return;
    await loadSettings();
    switch (workerConfig().page) {
        case 'shell': initShell(user); break;
        case 'overview': await initOverview(); break;
        case 'assigned': await initAssigned(); break;
        case 'in-progress': await initInProgress(); break;
        case 'resolved': await initResolved(); break;
        case 'profile': await initProfile(); break;
        case 'settings': await initSettingsPage(); break;
        case 'performance': await initPerformance(); break;
    }
});
