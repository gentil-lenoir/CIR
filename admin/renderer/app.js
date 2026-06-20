const API_BASE = 'http://127.0.0.1:8000/api/admin';
let currentTab = 'dashboard';
let currentWorkerId = null;
let currentDepartmentId = null;

async function api(path, options = {}) {
    const token = localStorage.getItem('admin_token');
    const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    const res = await fetch(`${API_BASE}${path}`, { ...options, headers: { ...headers, ...options.headers } });
    if (res.status === 401) {
        localStorage.removeItem('admin_token');
        showLogin();
        throw new Error('Unauthorized');
    }
    return res.json();
}

// Login
function showLogin() {
    document.getElementById('loginOverlay').classList.add('active');
    document.querySelector('.container').style.display = 'none';
}

async function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const errorEl = document.getElementById('loginError');
    try {
        const res = await fetch('http://127.0.0.1:8000/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email, password }),
        });
        const data = await res.json();
        if (!res.ok) {
            errorEl.style.display = 'block';
            errorEl.textContent = data.message || 'Login failed';
            return;
        }
        errorEl.style.display = 'none';
        localStorage.setItem('admin_token', data.access_token || data.data?.token);
        localStorage.setItem('admin_user', JSON.stringify(data.user || data.data?.user));
        document.getElementById('loginOverlay').classList.remove('active');
        document.querySelector('.container').style.display = '';
        loadDashboard();
    } catch (err) {
        errorEl.style.display = 'block';
        errorEl.textContent = 'Cannot connect to server at http://127.0.0.1:8000';
    }
}

function handleLogout() {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    showLogin();
}

// Tab navigation
document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', (e) => {
        const tab = link.dataset.tab;
        switchTab(tab);
        updateHeader(tab);
    });
});

function switchTab(tabName) {
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelector(`[data-section="${tabName}"]`).classList.add('active');

    currentTab = tabName;

    if (tabName === 'dashboard') {
        loadDashboard();
    } else if (tabName === 'workers') {
        loadWorkers();
    } else if (tabName === 'issues') {
        loadIssues();
    } else if (tabName === 'departments') {
        loadDepartments();
    }
}

function updateHeader(tab) {
    const headers = {
        dashboard: { eyebrow: 'Municipal Manager', heading: 'Dashboard' },
        workers: { eyebrow: 'Admin / Workers', heading: 'Worker Management' },
        issues: { eyebrow: 'Admin / Issues', heading: 'Issue Management' },
        departments: { eyebrow: 'Admin / Departments', heading: 'Department Management' },
        settings: { eyebrow: 'Account', heading: 'Settings' }
    };

    const header = headers[tab] || headers.dashboard;
    document.querySelector('.header-eyebrow').textContent = header.eyebrow;
    document.querySelector('.header-heading').textContent = header.heading;
}

// DASHBOARD
async function loadDashboard() {
    try {
        const statsResult = await api('/stats');
        if (statsResult.success) {
            const stats = statsResult.data;
            const cards = document.querySelectorAll('.stat-card');
            cards[0].querySelector('.stat-value').textContent = stats.workers;
            cards[0].querySelector('.stat-meta').textContent = `${stats.active_workers} active`;
            cards[1].querySelector('.stat-value').textContent = stats.departments;
            cards[2].querySelector('.stat-value').textContent = stats.reported_issues + stats.in_progress_issues;
            cards[2].querySelector('.stat-meta').textContent = `${stats.resolved_issues} resolved`;
            cards[3].querySelector('.stat-value').textContent = stats.overdue_issues;
        }

        const issuesResult = await api('/issues/recent');
        if (issuesResult.success) {
            const container = document.getElementById('recentIssuesContainer');
            const issues = issuesResult.data;
            if (issues.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No issues yet</p></div>';
            } else {
                let html = '<table style="width: 100%;"><tbody>';
                issues.forEach(issue => {
                    html += `
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px;">
                                <p style="font-weight: bold; margin: 0;">${issue.title}</p>
                                <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0;">${issue.category || 'General'}</p>
                            </td>
                            <td style="padding: 12px; color: #64748b; font-size: 12px;">
                                ${issue.worker_name || 'Unassigned'}
                            </td>
                            <td style="padding: 12px;">
                                <span class="badge badge-${issue.status}">${formatStatus(issue.status)}</span>
                            </td>
                        </tr>
                    `;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            }
        }

        const workersResult = await api('/workers/top');
        if (workersResult.success) {
            const container = document.getElementById('topWorkersContainer');
            const workers = workersResult.data;
            if (workers.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No workers yet</p></div>';
            } else {
                let html = '';
                workers.forEach(worker => {
                    html += `
                        <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <p style="font-weight: bold; margin: 0;">${worker.name}</p>
                                    <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0;">${worker.department_name || 'No dept'}</p>
                                </div>
                                <span style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                    ${worker.issues_count} issues
                                </span>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }
        }
    } catch (err) {
        console.error('Dashboard load error:', err);
    }
}

// WORKERS
async function loadWorkers() {
    try {
        const deptsResult = await api('/departments');
        if (deptsResult.success) {
            const filter = document.getElementById('workerDeptFilter');
            const deptSelect = document.getElementById('workerDept');
            filter.innerHTML = '<option value="">All departments</option>';
            deptSelect.innerHTML = '<option value="">No Department</option>';
            deptsResult.data.forEach(dept => {
                filter.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                deptSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
            });
        }

        const result = await api('/workers?page=1&limit=50');
        if (result.success) {
            const tbody = document.getElementById('workersBody');
            tbody.innerHTML = '';

            if (result.data.workers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">No workers found</td></tr>';
            } else {
                result.data.workers.forEach(worker => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${worker.name}</td>
                        <td style="font-size: 12px; color: #64748b;">${worker.email}</td>
                        <td>${worker.department_name || '-'}</td>
                        <td><span class="badge badge-${worker.status}">${worker.status}</span></td>
                        <td><span class="badge badge-${worker.availability_status === 'available' ? 'resolved' : 'in-progress'}">${worker.availability_status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editWorker(${worker.id})">Edit</button>
                            <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="deleteWorker(${worker.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }
    } catch (err) {
        console.error('Workers load error:', err);
    }
}

async function editWorker(id) {
    const result = await api('/workers?page=1&limit=50');
    const worker = result.data.workers.find(w => w.id === id);
    if (worker) {
        currentWorkerId = id;
        document.getElementById('workerName').value = worker.name;
        document.getElementById('workerEmail').value = worker.email;
        document.getElementById('workerPhone').value = worker.phone || '';
        document.getElementById('workerDept').value = worker.department_id || '';
        openModal('workerModal');
    }
}

async function deleteWorker(id) {
    if (confirm('Delete this worker?')) {
        const result = await api(`/workers/${id}`, { method: 'DELETE' });
        if (result.success) {
            alert('Worker deleted');
            loadWorkers();
        } else {
            alert('Error: ' + (result.error || 'Unknown error'));
        }
    }
}

// ISSUES
async function loadIssues() {
    try {
        const result = await api('/issues?page=1&limit=50');
        if (result.success) {
            const tbody = document.getElementById('issuesBody');
            tbody.innerHTML = '';

            if (result.data.issues.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">No issues found</td></tr>';
            } else {
                result.data.issues.forEach(issue => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${issue.title}</td>
                        <td style="font-size: 12px; color: #64748b;">${issue.category || '-'}</td>
                        <td>${issue.worker_name || 'Unassigned'}</td>
                        <td><span class="badge badge-${issue.status}">${formatStatus(issue.status)}</span></td>
                        <td style="font-size: 12px; color: #64748b;">${formatDate(issue.created_at)}</td>
                        <td>
                            <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewIssue(${issue.id})">View</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }
    } catch (err) {
        console.error('Issues load error:', err);
    }
}

// DEPARTMENTS
async function loadDepartments() {
    try {
        const result = await api('/departments');
        if (result.success) {
            const tbody = document.getElementById('departmentsBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 40px;">No departments found</td></tr>';
            } else {
                result.data.forEach(dept => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${dept.name}</td>
                        <td style="font-size: 12px; color: #64748b;">${dept.description || '-'}</td>
                        <td>0</td>
                        <td>
                            <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editDepartment(${dept.id})">Edit</button>
                            <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="deleteDepartment(${dept.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }
    } catch (err) {
        console.error('Departments load error:', err);
    }
}

function editDepartment(id) {
    currentDepartmentId = id;
    openModal('departmentModal');
}

async function deleteDepartment(id) {
    if (confirm('Delete this department?')) {
        const result = await api(`/departments/${id}`, { method: 'DELETE' });
        if (result.success) {
            alert('Department deleted');
            loadDepartments();
        } else {
            alert('Error: ' + (result.error || 'Unknown error'));
        }
    }
}

// MODALS
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// FORMS
document.getElementById('workerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const worker = {
        name: document.getElementById('workerName').value,
        email: document.getElementById('workerEmail').value,
        phone: document.getElementById('workerPhone').value,
        department_id: document.getElementById('workerDept').value || null
    };

    const result = currentWorkerId
        ? await api(`/workers/${currentWorkerId}`, { method: 'PUT', body: JSON.stringify(worker) })
        : await api('/workers', { method: 'POST', body: JSON.stringify(worker) });

    if (result.success) {
        alert('Worker saved');
        closeModal('workerModal');
        loadWorkers();
        currentWorkerId = null;
    } else {
        alert('Error: ' + (result.error || 'Unknown error'));
    }
});

document.getElementById('departmentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const dept = {
        name: document.getElementById('deptName').value,
        description: document.getElementById('deptDesc').value
    };

    const result = currentDepartmentId
        ? await api(`/departments/${currentDepartmentId}`, { method: 'PUT', body: JSON.stringify(dept) })
        : await api('/departments', { method: 'POST', body: JSON.stringify(dept) });

    if (result.success) {
        alert('Department saved');
        closeModal('departmentModal');
        loadDepartments();
        currentDepartmentId = null;
    } else {
        alert('Error: ' + (result.error || 'Unknown error'));
    }
});

// BUTTON ACTIONS
document.querySelectorAll('[data-action]').forEach(btn => {
    btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        if (action === 'add-worker') {
            currentWorkerId = null;
            document.getElementById('workerForm').reset();
            openModal('workerModal');
        } else if (action === 'add-department') {
            currentDepartmentId = null;
            document.getElementById('departmentForm').reset();
            openModal('departmentModal');
        }
    });
});

// Logout button
document.querySelector('.sidebar-footer .user-card')?.addEventListener('click', handleLogout);

// UTILITIES
function formatStatus(status) {
    return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
}

function viewIssue(id) {
    alert('Issue view not yet implemented');
}

// INIT
document.getElementById('loginForm').addEventListener('submit', handleLogin);
if (localStorage.getItem('admin_token')) {
    document.getElementById('loginOverlay').classList.remove('active');
    document.querySelector('.container').style.display = '';
    loadDashboard();
} else {
    showLogin();
}
