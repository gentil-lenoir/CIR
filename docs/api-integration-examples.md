# Smart Public Issue Reporting API Integration

Base URL: `http://127.0.0.1:8000/api`

Use token from login/register in every protected request:

```http
Authorization: Bearer {token}
Accept: application/json
```

## 1) Register

```js
const response = await fetch('/api/register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
  body: JSON.stringify({
    name: 'Rahul Sharma',
    email: 'rahul@example.com',
    phone: '9999999999',
    password: 'Password@123',
    password_confirmation: 'Password@123'
  })
});
const result = await response.json();
localStorage.setItem('token', result.data.token);
```

## 2) Login

```js
import axios from 'axios';

const { data } = await axios.post('/api/login', {
  email: 'rahul@example.com',
  password: 'Password@123'
});

localStorage.setItem('token', data.data.token);
```

## 3) Report Issue (with image + location)

```js
const formData = new FormData();
formData.append('title', 'Water leakage on main road');
formData.append('description', 'Continuous leakage near ward office.');
formData.append('priority', 'high');
formData.append('category', 'water');
formData.append('latitude', '18.520430');
formData.append('longitude', '73.856744');
formData.append('address', 'MG Road, Pune');
formData.append('images[]', fileInput.files[0]);

await fetch('/api/issues', {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
    Accept: 'application/json'
  },
  body: formData
});
```

## 4) Get Issues

```js
const issues = await fetch('/api/issues?status=reported', {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
}).then((r) => r.json());
```

## 5) Assign Issue (Admin)

```js
await axios.post(
  '/api/municipal-manager/assign-issue',
  {
    issue_id: 14,
    worker_id: 7,
    priority: 'high',
    notes: 'Handle this today'
  },
  {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  }
);
```

## 6) Add Comment

```js
await fetch('/api/comments', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${localStorage.getItem('token')}`,
    Accept: 'application/json'
  },
  body: JSON.stringify({
    issue_id: 14,
    comment: 'Team has inspected the site.'
  })
});
```

## 7) Upvote

```js
await fetch('/api/issues/14/upvote', {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
    Accept: 'application/json'
  }
});
```

## 8) Notifications

```js
const notifications = await axios.get('/api/notifications', {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
});
```

## Sample JSON Responses

### Login Success

```json
{
  "message": "Login successful.",
  "data": {
    "user": {
      "id": 5,
      "name": "Rahul Sharma",
      "email": "rahul@example.com",
      "role": "citizen"
    },
    "token": "1|9h3B..."
  }
}
```

### Issue Create Success

```json
{
  "id": 14,
  "title": "Water leakage on main road",
  "description": "Continuous leakage near ward office.",
  "status": "reported",
  "priority": "high",
  "location": {
    "latitude": 18.52043,
    "longitude": 73.856744,
    "address": "MG Road, Pune"
  },
  "upvotes_count": 0
}
```

### Notification List

```json
{
  "message": "Notifications fetched successfully.",
  "data": [
    {
      "id": 45,
      "issue_id": 14,
      "type": "issue_assigned",
      "title": "Issue Assigned",
      "message": "Your issue #14 has been assigned to a municipal worker.",
      "is_read": false
    }
  ]
}
```
