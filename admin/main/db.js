const Database = require('better-sqlite3');
const path = require('node:path');
const electron = require('electron')
const { app } = electron
const os = require('os');

let db = null;

function initDatabase() {
  try {
    let dbPath;
    try {
      // Try to use app.getPath if app is ready
      dbPath = path.join(app.getPath('userData'), 'admin.db');
    } catch (e) {
      // Fallback if app not ready
      dbPath = path.join(os.homedir(), '.cis-admin', 'admin.db');
    }
    
    // Ensure directory exists
    const dir = path.dirname(dbPath);
    require('fs').mkdirSync(dir, { recursive: true });
    
    db = new Database(dbPath);
    db.pragma('journal_mode = WAL');
    
    createTables();
    return db;
  } catch (err) {
    console.error('Database init error:', err);
    throw err;
  }
}

function createTables() {
  db.exec(`
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY,
      name TEXT NOT NULL,
      email TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      theme_preference TEXT DEFAULT 'light',
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS departments (
      id INTEGER PRIMARY KEY,
      name TEXT NOT NULL,
      description TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS workers (
      id INTEGER PRIMARY KEY,
      name TEXT NOT NULL,
      email TEXT UNIQUE NOT NULL,
      phone TEXT,
      department_id INTEGER,
      status TEXT DEFAULT 'active',
      availability_status TEXT DEFAULT 'available',
      theme_preference TEXT DEFAULT 'light',
      open_issues_count INTEGER DEFAULT 0,
      resolved_issues_count INTEGER DEFAULT 0,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY(department_id) REFERENCES departments(id)
    );

    CREATE TABLE IF NOT EXISTS issues (
      id INTEGER PRIMARY KEY,
      title TEXT NOT NULL,
      description TEXT,
      category TEXT,
      status TEXT DEFAULT 'reported',
      user_id INTEGER,
      worker_id INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY(user_id) REFERENCES users(id),
      FOREIGN KEY(worker_id) REFERENCES workers(id)
    );

    CREATE TABLE IF NOT EXISTS settings (
      id INTEGER PRIMARY KEY,
      key TEXT UNIQUE NOT NULL,
      value TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
  `);
}

function getDatabase() {
  return db;
}

module.exports = { initDatabase, getDatabase };
