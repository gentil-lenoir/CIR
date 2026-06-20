const express = require('express')
const path = require('path')
const fs = require('fs')
const app = express()

// Middleware
app.use(express.json())
app.use(express.static(path.join(__dirname, '..')))

// Simple in-memory database
const db = {
  workers: [
    { id: 1, name: 'John Worker', email: 'john@admin.local', department_id: 1, status: 'active', availability_status: 'available' }
  ],
  departments: [
    { id: 1, name: 'Street Maintenance' }
  ],
  issues: [
    { id: 1, title: 'Pothole on Main St', status: 'reported', worker_id: 1 }
  ]
}

// Dashboard stats
app.get('/api/stats', (req, res) => {
  res.json({
    workers: db.workers.length,
    active_workers: db.workers.filter(w => w.status === 'active').length,
    departments: db.departments.length,
    reported_issues: db.issues.filter(i => i.status === 'reported').length,
    in_progress_issues: db.issues.filter(i => i.status === 'in_progress').length,
    resolved_issues: db.issues.filter(i => i.status === 'resolved').length,
    overdue_issues: 0
  })
})

// Workers API
app.get('/api/workers', (req, res) => {
  res.json(db.workers)
})

app.post('/api/workers', (req, res) => {
  const worker = { id: Date.now(), ...req.body, status: 'active', availability_status: 'available' }
  db.workers.push(worker)
  res.json(worker)
})

app.put('/api/workers/:id', (req, res) => {
  const idx = db.workers.findIndex(w => w.id == req.params.id)
  if (idx >= 0) {
    db.workers[idx] = { ...db.workers[idx], ...req.body }
    res.json(db.workers[idx])
  } else {
    res.status(404).json({ error: 'Not found' })
  }
})

app.delete('/api/workers/:id', (req, res) => {
  const idx = db.workers.findIndex(w => w.id == req.params.id)
  if (idx >= 0) {
    db.workers.splice(idx, 1)
    res.json({ success: true })
  } else {
    res.status(404).json({ error: 'Not found' })
  }
})

// Departments API
app.get('/api/departments', (req, res) => {
  res.json(db.departments)
})

app.post('/api/departments', (req, res) => {
  const dept = { id: Date.now(), ...req.body }
  db.departments.push(dept)
  res.json(dept)
})

// Issues API
app.get('/api/issues', (req, res) => {
  res.json(db.issues)
})

app.get('/api/issues/recent', (req, res) => {
  res.json(db.issues.slice(0, 10))
})

// Workers top
app.get('/api/workers/top', (req, res) => {
  res.json(db.workers.slice(0, 5))
})

const PORT = process.env.PORT || 5555
app.listen(PORT, () => {
  console.log(`Admin API listening on http://localhost:${PORT}`)
})
