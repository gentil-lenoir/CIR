let currentTab = 'dashboard';
let currentWorkerId = null;
let currentDepartmentId = null;

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
        const statsResult = await window.electronAPI.getDashboardStats();
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

        const issuesResult = await window.electronAPI.getRecentIssues();
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

        const workersResult = await window.electronAPI.getTopWorkers();
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
        // Load departments for filter
        const deptsResult = await window.electronAPI.listDepartments();
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

        // Load workers
        const result = await window.electronAPI.listWorkers({ page: 1, limit: 50 });
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
    const result = await window.electronAPI.listWorkers({ page: 1, limit: 50 });
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
        const result = await window.electronAPI.deleteWorker(id);
        if (result.success) {
            alert('Worker deleted');
            loadWorkers();
        } else {
            alert('Error: ' + result.error);
        }
    }
}

// ISSUES
async function loadIssues() {
    try {
        const result = await window.electronAPI.listIssues({ page: 1, limit: 50 });
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
        const result = await window.electronAPI.listDepartments();
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
    // Fetch and populate form (simplified for now)
    openModal('departmentModal');
}

async function deleteDepartment(id) {
    if (confirm('Delete this department?')) {
        const result = await window.electronAPI.deleteDepartment(id);
        if (result.success) {
            alert('Department deleted');
            loadDepartments();
        } else {
            alert('Error: ' + result.error);
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
        ? await window.electronAPI.updateWorker(currentWorkerId, worker)
        : await window.electronAPI.createWorker(worker);

    if (result.success) {
        alert('Worker saved');
        closeModal('workerModal');
        loadWorkers();
        currentWorkerId = null;
    } else {
        alert('Error: ' + result.error);
    }
});

document.getElementById('departmentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const dept = {
        name: document.getElementById('deptName').value,
        description: document.getElementById('deptDesc').value
    };

    const result = currentDepartmentId
        ? await window.electronAPI.updateDepartment(currentDepartmentId, dept)
        : await window.electronAPI.createDepartment(dept);

    if (result.success) {
        alert('Department saved');
        closeModal('departmentModal');
        loadDepartments();
        currentDepartmentId = null;
    } else {
        alert('Error: ' + result.error);
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

// UTILITIES
function formatStatus(status) {
    return status.replace('_', ' ').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function viewIssue(id) {
    alert('Issue view not yet implemented');
}

// Initial load
loadDashboard();
