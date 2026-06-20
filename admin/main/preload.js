const electron = require('electron')
const { contextBridge, ipcRenderer } = electron

contextBridge.exposeInMainWorld('electronAPI', {
  // Dashboard
  getDashboardStats: () => ipcRenderer.invoke('dashboard:getStats'),
  getRecentIssues: () => ipcRenderer.invoke('issues:getRecent'),
  getTopWorkers: () => ipcRenderer.invoke('workers:getTop'),

  // Workers
  listWorkers: (params) => ipcRenderer.invoke('workers:list', params),
  createWorker: (worker) => ipcRenderer.invoke('workers:create', worker),
  updateWorker: (id, worker) => ipcRenderer.invoke('workers:update', { id, worker }),
  deleteWorker: (id) => ipcRenderer.invoke('workers:delete', id),
  toggleWorkerStatus: (id) => ipcRenderer.invoke('workers:toggleStatus', id),

  // Departments
  listDepartments: () => ipcRenderer.invoke('departments:list'),
  createDepartment: (dept) => ipcRenderer.invoke('departments:create', dept),
  updateDepartment: (id, dept) => ipcRenderer.invoke('departments:update', { id, dept }),
  deleteDepartment: (id) => ipcRenderer.invoke('departments:delete', id),

  // Issues
  listIssues: (params) => ipcRenderer.invoke('issues:list', params),
  updateIssue: (id, issue) => ipcRenderer.invoke('issues:update', { id, issue })
})
