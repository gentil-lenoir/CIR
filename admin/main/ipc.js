const { ipcMain } = require('electron');
const { getDatabase } = require('./db');

function setupIPC() {
  // Dashboard stats
  ipcMain.handle('dashboard:getStats', () => {
    const db = getDatabase();
    try {
      const stats = {
        workers: db.prepare('SELECT COUNT(*) as count FROM workers').get().count,
        active_workers: db.prepare("SELECT COUNT(*) as count FROM workers WHERE status = 'active'").get().count,
        departments: db.prepare('SELECT COUNT(*) as count FROM departments').get().count,
        reported_issues: db.prepare("SELECT COUNT(*) as count FROM issues WHERE status = 'reported'").get().count,
        in_progress_issues: db.prepare("SELECT COUNT(*) as count FROM issues WHERE status = 'in_progress'").get().count,
        resolved_issues: db.prepare("SELECT COUNT(*) as count FROM issues WHERE status = 'resolved'").get().count,
        overdue_issues: db.prepare("SELECT COUNT(*) as count FROM issues WHERE status IN ('reported', 'in_progress') AND julianday(updated_at) < julianday('now', '-7 days')").get().count,
      };
      return { success: true, data: stats };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Recent issues
  ipcMain.handle('issues:getRecent', () => {
    const db = getDatabase();
    try {
      const issues = db.prepare(`
        SELECT i.*, w.name as worker_name, d.name as department_name
        FROM issues i
        LEFT JOIN workers w ON i.worker_id = w.id
        LEFT JOIN departments d ON w.department_id = d.id
        ORDER BY i.created_at DESC
        LIMIT 10
      `).all();
      return { success: true, data: issues };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Top workers
  ipcMain.handle('workers:getTop', () => {
    const db = getDatabase();
    try {
      const workers = db.prepare(`
        SELECT w.*, d.name as department_name, COUNT(i.id) as issues_count
        FROM workers w
        LEFT JOIN departments d ON w.department_id = d.id
        LEFT JOIN issues i ON w.id = i.worker_id
        GROUP BY w.id
        ORDER BY issues_count DESC
        LIMIT 5
      `).all();
      return { success: true, data: workers };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Workers list
  ipcMain.handle('workers:list', (event, { search, department, status, page = 1, limit = 10 }) => {
    const db = getDatabase();
    try {
      let query = `
        SELECT w.*, d.name as department_name
        FROM workers w
        LEFT JOIN departments d ON w.department_id = d.id
        WHERE 1=1
      `;
      const params = [];

      if (search) {
        query += ` AND (w.name LIKE ? OR w.email LIKE ?)`;
        params.push(`%${search}%`, `%${search}%`);
      }
      if (department) {
        query += ` AND w.department_id = ?`;
        params.push(department);
      }
      if (status) {
        query += ` AND w.status = ?`;
        params.push(status);
      }

      const offset = (page - 1) * limit;
      const countStmt = db.prepare(`SELECT COUNT(*) as count FROM workers w LEFT JOIN departments d ON w.department_id = d.id WHERE 1=1 ${search ? 'AND (w.name LIKE ? OR w.email LIKE ?)' : ''} ${department ? 'AND w.department_id = ?' : ''} ${status ? 'AND w.status = ?' : ''}`);
      const total = countStmt.get(...params).count;

      query += ` LIMIT ? OFFSET ?`;
      const stmt = db.prepare(query);
      const workers = stmt.all(...params, limit, offset);

      return { success: true, data: { workers, total, page, limit } };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Worker CRUD
  ipcMain.handle('workers:create', (event, worker) => {
    const db = getDatabase();
    try {
      const stmt = db.prepare(`
        INSERT INTO workers (name, email, phone, department_id, status, availability_status, theme_preference)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      `);
      const result = stmt.run(
        worker.name,
        worker.email,
        worker.phone || null,
        worker.department_id || null,
        worker.status || 'active',
        worker.availability_status || 'available',
        worker.theme_preference || 'light'
      );
      return { success: true, data: { id: result.lastInsertRowid } };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('workers:update', (event, { id, worker }) => {
    const db = getDatabase();
    try {
      const stmt = db.prepare(`
        UPDATE workers
        SET name = ?, email = ?, phone = ?, department_id = ?, status = ?, availability_status = ?, theme_preference = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
      `);
      stmt.run(
        worker.name,
        worker.email,
        worker.phone || null,
        worker.department_id || null,
        worker.status,
        worker.availability_status,
        worker.theme_preference,
        id
      );
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('workers:delete', (event, id) => {
    const db = getDatabase();
    try {
      db.prepare('DELETE FROM workers WHERE id = ?').run(id);
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('workers:toggleStatus', (event, id) => {
    const db = getDatabase();
    try {
      const worker = db.prepare('SELECT status FROM workers WHERE id = ?').get(id);
      const newStatus = worker.status === 'active' ? 'inactive' : 'active';
      db.prepare('UPDATE workers SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?').run(newStatus, id);
      return { success: true, data: { status: newStatus } };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Departments
  ipcMain.handle('departments:list', () => {
    const db = getDatabase();
    try {
      const departments = db.prepare('SELECT * FROM departments ORDER BY name').all();
      return { success: true, data: departments };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('departments:create', (event, dept) => {
    const db = getDatabase();
    try {
      const stmt = db.prepare(`
        INSERT INTO departments (name, description)
        VALUES (?, ?)
      `);
      const result = stmt.run(dept.name, dept.description || null);
      return { success: true, data: { id: result.lastInsertRowid } };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('departments:update', (event, { id, dept }) => {
    const db = getDatabase();
    try {
      db.prepare('UPDATE departments SET name = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
        .run(dept.name, dept.description || null, id);
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('departments:delete', (event, id) => {
    const db = getDatabase();
    try {
      db.prepare('DELETE FROM departments WHERE id = ?').run(id);
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  // Issues
  ipcMain.handle('issues:list', (event, { search, status, page = 1, limit = 10 }) => {
    const db = getDatabase();
    try {
      let query = `
        SELECT i.*, w.name as worker_name, d.name as department_name
        FROM issues i
        LEFT JOIN workers w ON i.worker_id = w.id
        LEFT JOIN departments d ON w.department_id = d.id
        WHERE 1=1
      `;
      const params = [];

      if (search) {
        query += ` AND (i.title LIKE ? OR i.description LIKE ?)`;
        params.push(`%${search}%`, `%${search}%`);
      }
      if (status) {
        query += ` AND i.status = ?`;
        params.push(status);
      }

      const offset = (page - 1) * limit;
      const countStmt = db.prepare(`SELECT COUNT(*) as count FROM issues i WHERE 1=1 ${search ? 'AND (i.title LIKE ? OR i.description LIKE ?)' : ''} ${status ? 'AND i.status = ?' : ''}`);
      const total = countStmt.get(...params).count;

      query += ` ORDER BY i.created_at DESC LIMIT ? OFFSET ?`;
      const stmt = db.prepare(query);
      const issues = stmt.all(...params, limit, offset);

      return { success: true, data: { issues, total, page, limit } };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });

  ipcMain.handle('issues:update', (event, { id, issue }) => {
    const db = getDatabase();
    try {
      db.prepare(`
        UPDATE issues
        SET title = ?, description = ?, category = ?, status = ?, worker_id = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
      `).run(issue.title, issue.description, issue.category, issue.status, issue.worker_id || null, id);
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  });
}

module.exports = { setupIPC };
