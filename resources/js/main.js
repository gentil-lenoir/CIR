const DEFAULT_FALLBACK_IMAGE =
    "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500'%3E%3Crect width='800' height='500' fill='%23e2e8f0'/%3E%3Ccircle cx='250' cy='180' r='60' fill='%2394a3b8'/%3E%3Cpath d='M120 390l150-130 85 75 115-125 210 180H120z' fill='%2364758b'/%3E%3Ctext x='50%25' y='90%25' text-anchor='middle' font-family='Public Sans, sans-serif' font-size='30' fill='%230f172a'%3EIssue image unavailable%3C/text%3E%3C/svg%3E";

function getToken() {
    return localStorage.getItem('jwt_token') || localStorage.getItem('token') || '';
}

function redirectToLogin(url) {
    window.location.href = url;
}

async function fetchJson(url, { authRequired = false } = {}) {
    const token = getToken();
    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    };

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(url, {
        method: 'GET',
        headers,
    });

    if (response.status === 401) {
        if (authRequired) {
            const loginUrl = document.querySelector('[data-login-url]')?.dataset.loginUrl || '/login';
            redirectToLogin(loginUrl);
        }

        throw new Error('Unauthorized');
    }

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(payload.message || 'Request failed.');
    }

    return payload;
}

function handleAuthUI() {
    const guestLinks = document.querySelectorAll('.auth-guest-link');

    guestLinks.forEach((link) => {
        link.classList.remove('hidden');
    });
}

function renderIssueCard(issue) {
    const imageUrl = issue?.images?.[0]?.url || DEFAULT_FALLBACK_IMAGE;
    const status = String(issue?.status || 'unknown').toLowerCase().replace(/_/g, '-');
    const statusClasses = {
        reported: 'landing-status-reported',
        assigned: 'landing-status-assigned',
        'in-progress': 'landing-status-in-progress',
        resolved: 'landing-status-resolved',
    };
    const badgeClass = statusClasses[status] || 'landing-status-default';
    const safeTitle = issue?.title || 'Untitled issue';
    const safeCopy = issue?.description || 'No additional details available yet.';
    const upvotes = Number(issue?.upvotes_count || 0);

    return `
        <article class="landing-issue-card">
            <img class="landing-issue-image js-fallback-image" src="${imageUrl}" alt="${safeTitle}" loading="lazy">
            <div class="p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <span class="landing-status-badge ${badgeClass}">${status.replace(/-/g, ' ')}</span>
                    <span class="landing-upvote-chip">
                        <span class="material-symbols-outlined text-base">thumb_up</span>
                        ${upvotes}
                    </span>
                </div>
                <h3 class="landing-issue-title mb-3">${safeTitle}</h3>
                <p class="landing-issue-copy mb-5">${safeCopy}</p>
                <div class="landing-issue-meta">
                    <span>${issue?.location?.address || 'Location available in map view'}</span>
                    <span>#${issue?.id ?? '--'}</span>
                </div>
            </div>
        </article>
    `;
}

function applyImageFallbacks(scope = document) {
    scope.querySelectorAll('.js-fallback-image').forEach((image) => {
        image.addEventListener(
            'error',
            () => {
                image.src = DEFAULT_FALLBACK_IMAGE;
            },
            { once: true },
        );
    });
}

function setFeedback(elementId, message, visible) {
    const element = document.getElementById(elementId);

    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.toggle('hidden', !visible);
}

async function fetchIssues() {
    const issuesGrid = document.getElementById('recent-issues-grid');

    if (!issuesGrid) {
        return;
    }

    try {
        const payload = await fetchJson(issuesGrid.dataset.issuesUrl);
        const issues = Array.isArray(payload?.data) ? payload.data : [];

        if (!issues.length) {
            issuesGrid.innerHTML = `
                <div class="landing-empty-state">
                    No issues have been reported yet. Be the first citizen to raise one.
                </div>
            `;
            return;
        }

        issuesGrid.innerHTML = issues.map(renderIssueCard).join('');
        applyImageFallbacks(issuesGrid);
        setFeedback('issues-feedback', '', false);
    } catch (error) {
        issuesGrid.innerHTML = `
            <div class="landing-empty-state">
                We could not load recent issues right now. Please try again shortly.
            </div>
        `;

        setFeedback(
            'issues-feedback',
            error.message === 'Unauthorized'
                ? 'Please sign in to continue.'
                : 'Network issue detected while loading recent reports.',
            true,
        );
    }
}

async function fetchStats() {
    const statsPanel = document.querySelector('.landing-stats-panel');

    if (!statsPanel) {
        return;
    }

    try {
        const payload = await fetchJson(statsPanel.dataset.statsUrl);
        const stats = payload?.data || {};
        const total = Number(stats.total_issues ?? 0);
        const resolved = Number(stats.resolved_issues ?? 0);
        const pending = Number(stats.pending_issues ?? 0);

        document.getElementById('total-issues-value').textContent = total;
        document.getElementById('resolved-issues-value').textContent = resolved;
        document.getElementById('pending-issues-value').textContent = pending;

        document.querySelectorAll('[data-stat-value="total"]').forEach((node) => {
            node.textContent = total;
        });
        document.querySelectorAll('[data-stat-value="resolved"]').forEach((node) => {
            node.textContent = resolved;
        });
        document.querySelectorAll('[data-stat-value="pending"]').forEach((node) => {
            node.textContent = pending;
        });

        setFeedback('stats-feedback', '', false);
    } catch (error) {
        document.getElementById('total-issues-value').textContent = '--';
        document.getElementById('resolved-issues-value').textContent = '--';
        document.getElementById('pending-issues-value').textContent = '--';

        setFeedback(
            'stats-feedback',
            error.message === 'Unauthorized'
                ? 'Please sign in to view protected statistics.'
                : 'Unable to load live system stats right now.',
            true,
        );
    }
}

function handleCTAButtons() {
    const reportButton = document.getElementById('report-issue-btn');
    const getStartedButton = document.getElementById('get-started-btn');
    const mapButton = document.getElementById('view-map-btn');
    const browseButton = document.getElementById('browse-issues-btn');

    reportButton?.addEventListener('click', () => {
        const loginUrl = reportButton.dataset.loginUrl || '/login';
        window.location.href = loginUrl;
    });

    getStartedButton?.addEventListener('click', () => {
        const loginUrl = getStartedButton.dataset.loginUrl || '/login';
        window.location.href = loginUrl;
    });

    mapButton?.addEventListener('click', () => {
        window.location.href = mapButton.dataset.mapUrl || '/citizen/map';
    });

    browseButton?.addEventListener('click', () => {
        window.location.href = browseButton.dataset.issuesUrl || '/citizen/my-issues';
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    handleAuthUI();
    handleCTAButtons();
    applyImageFallbacks();
    await Promise.all([fetchIssues(), fetchStats()]);
});

export { getToken, handleAuthUI, fetchIssues, fetchStats, handleCTAButtons };
