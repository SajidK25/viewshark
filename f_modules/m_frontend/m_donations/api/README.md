# ViewShark Donations API Documentation

This API allows external applications to interact with the ViewShark donation system. All requests require authentication using an API key.

## Authentication

All API requests must include an API key in the Authorization header:

```
Authorization: your-api-key-here
```

## Base URL

```
https://your-domain.com/f_modules/m_frontend/m_donations/api
```

## Endpoints

### Analytics

#### Get Analytics Data
```http
GET /analytics
```

Query Parameters:
- `start_date` (optional): Start date in YYYY-MM-DD format (default: 30 days ago)
- `end_date` (optional): End date in YYYY-MM-DD format (default: today)

Response:
```json
[
  {
    "date": "2024-03-20",
    "total_donations": 5,
    "total_amount": 150.00,
    "average_donation": 30.00,
    "unique_donors": 3
  }
]
```

#### Get Analytics Summary
```http
GET /analytics/summary
```

Response:
```json
{
  "total_donations": 150,
  "total_amount": 5000.00,
  "average_donation": 33.33,
  "unique_donors": 45,
  "largest_donation": 200.00,
  "smallest_donation": 5.00
}
```

#### Get Top Donors
```http
GET /analytics/top-donors
```

Query Parameters:
- `limit` (optional): Number of top donors to return (default: 10)

Response:
```json
[
  {
    "username": "donor1",
    "display_name": "John Doe",
    "donation_count": 15,
    "total_amount": 500.00
  }
]
```

### Goals

#### Get All Goals
```http
GET /goals
```

Response:
```json
[
  {
    "goal_id": 1,
    "title": "New Equipment",
    "description": "Help me upgrade my streaming setup",
    "target_amount": 1000.00,
    "current_amount": 500.00,
    "start_date": "2024-03-01T00:00:00Z",
    "end_date": "2024-04-01T00:00:00Z",
    "status": "active"
  }
]
```

#### Create New Goal
```http
POST /goals
```

Request Body:
```json
{
  "title": "New Equipment",
  "description": "Help me upgrade my streaming setup",
  "target_amount": 1000.00,
  "end_date": "2024-04-01"
}
```

Response:
```json
{
  "success": true,
  "goal_id": 1
}
```

#### Get Active Goals
```http
GET /goals/active
```

Response:
```json
[
  {
    "goal_id": 1,
    "title": "New Equipment",
    "description": "Help me upgrade my streaming setup",
    "target_amount": 1000.00,
    "current_amount": 500.00,
    "start_date": "2024-03-01T00:00:00Z",
    "end_date": "2024-04-01T00:00:00Z",
    "status": "active"
  }
]
```

#### Add Milestone to Goal
```http
POST /goals/milestones
```

Request Body:
```json
{
  "goal_id": 1,
  "title": "Halfway There",
  "description": "Reach 50% of the goal",
  "target_amount": 500.00,
  "reward_description": "Special shoutout on stream"
}
```

Response:
```json
{
  "success": true,
  "milestone_id": 1
}
```

### Notifications

#### Get All Notifications
```http
GET /notifications
```

Query Parameters:
- `limit` (optional): Number of notifications to return (default: 20)

Response:
```json
[
  {
    "notification_id": 1,
    "type": "donation",
    "title": "New Donation",
    "message": "Received $50.00 from John Doe",
    "is_read": false,
    "created_at": "2024-03-20T15:30:00Z"
  }
]
```

#### Get Unread Notifications
```http
GET /notifications/unread
```

Query Parameters:
- `limit` (optional): Number of notifications to return (default: 10)

Response:
```json
[
  {
    "notification_id": 1,
    "type": "donation",
    "title": "New Donation",
    "message": "Received $50.00 from John Doe",
    "is_read": false,
    "created_at": "2024-03-20T15:30:00Z"
  }
]
```

#### Mark Notifications as Read
```http
POST /notifications/unread
```

Request Body:
```json
{
  "notification_ids": [1, 2, 3]
}
```

Response:
```json
{
  "success": true
}
```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "error": "Missing required fields"
}
```

### 401 Unauthorized
```json
{
  "error": "API key is required"
}
```
or
```json
{
  "error": "Invalid API key"
}
```

### 404 Not Found
```json
{
  "error": "Endpoint not found"
}
```

### 405 Method Not Allowed
```json
{
  "error": "Method not allowed"
}
```

## Rate Limiting

The API is rate limited to 100 requests per minute per API key. When the rate limit is exceeded, the API will return a 429 Too Many Requests response:

```json
{
  "error": "Rate limit exceeded"
}
```

## Best Practices

1. Always include error handling in your API calls
2. Cache responses when appropriate to reduce API calls
3. Use appropriate HTTP methods (GET for retrieving data, POST for creating)
4. Include proper error messages in your application when API calls fail
5. Keep your API key secure and never expose it in client-side code 