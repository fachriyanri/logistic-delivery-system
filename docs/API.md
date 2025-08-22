# API Documentation

## Overview

The CodeIgniter Logistics System provides a RESTful API for managing logistics operations. The API supports authentication, shipment management, QR code operations, and data retrieval for integration with external systems.

## Base URL

```
https://your-domain.com/api/v1
```

## Authentication

### API Authentication
All API endpoints require authentication using session-based authentication or API tokens.

#### Login Endpoint
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "username": "adminpuninar",
    "password": "AdminPuninar123"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user_id": "USR01",
        "username": "adminpuninar",
        "level": 1,
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

#### Logout Endpoint
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "message": "Logout successful"
}
```

## User Management API

### Get Current User
```http
GET /api/v1/user/profile
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": {
        "id_user": "USR01",
        "username": "adminpuninar",
        "level": 1,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
    }
}
```

### Update Password
```http
PUT /api/v1/user/password
Authorization: Bearer {token}
Content-Type: application/json

{
    "current_password": "current_password",
    "new_password": "new_password",
    "confirm_password": "new_password"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Password updated successfully"
}
```

## Shipment Management API

### Get Shipments
```http
GET /api/v1/shipments
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `limit` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status (0=Pending, 1=Delivered, 2=Cancelled)
- `date_from` (optional): Start date filter (YYYY-MM-DD)
- `date_to` (optional): End date filter (YYYY-MM-DD)
- `customer_id` (optional): Filter by customer ID
- `courier_id` (optional): Filter by courier ID

**Response:**
```json
{
    "status": "success",
    "data": {
        "shipments": [
            {
                "id_pengiriman": "KRM20240101001",
                "tanggal": "2024-01-01",
                "customer": {
                    "id_pelanggan": "CST0001",
                    "nama": "ASTRA OTOPART",
                    "telepon": "021-4603550",
                    "alamat": "jakarta"
                },
                "courier": {
                    "id_kurir": "KRR01",
                    "nama": "EKO",
                    "telepon": "081385195955"
                },
                "no_kendaraan": "B021ZIG",
                "no_po": "PO123456",
                "status": 1,
                "status_text": "Delivered",
                "items": [
                    {
                        "id_barang": "BRG0001",
                        "nama": "BRAKE SHOE HONDA ASP",
                        "qty": 5,
                        "satuan": "SATUAN 1"
                    }
                ]
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 150,
            "total_pages": 8
        }
    }
}
```

### Get Single Shipment
```http
GET /api/v1/shipments/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": {
        "id_pengiriman": "KRM20240101001",
        "tanggal": "2024-01-01",
        "customer": {
            "id_pelanggan": "CST0001",
            "nama": "ASTRA OTOPART",
            "telepon": "021-4603550",
            "alamat": "jakarta"
        },
        "courier": {
            "id_kurir": "KRR01",
            "nama": "EKO",
            "telepon": "081385195955"
        },
        "no_kendaraan": "B021ZIG",
        "no_po": "PO123456",
        "keterangan": "Delivery notes",
        "penerima": "John Doe",
        "photo": "delivery_photo.jpg",
        "status": 1,
        "status_text": "Delivered",
        "items": [
            {
                "id_detail": 1,
                "id_barang": "BRG0001",
                "nama": "BRAKE SHOE HONDA ASP",
                "qty": 5,
                "satuan": "SATUAN 1",
                "kategori": "KATEGORI 1"
            }
        ],
        "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
    }
}
```

### Create Shipment
```http
POST /api/v1/shipments
Authorization: Bearer {token}
Content-Type: application/json

{
    "tanggal": "2024-01-01",
    "id_pelanggan": "CST0001",
    "id_kurir": "KRR01",
    "no_kendaraan": "B021ZIG",
    "no_po": "PO123456",
    "keterangan": "Delivery notes",
    "items": [
        {
            "id_barang": "BRG0001",
            "qty": 5
        },
        {
            "id_barang": "BRG0002",
            "qty": 3
        }
    ]
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Shipment created successfully",
    "data": {
        "id_pengiriman": "KRM20240101001",
        "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
    }
}
```

### Update Shipment Status
```http
PUT /api/v1/shipments/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": 1,
    "penerima": "John Doe",
    "photo": "base64_encoded_image_data"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Shipment status updated successfully"
}
```

## QR Code API

### Generate QR Code
```http
POST /api/v1/qr/generate
Authorization: Bearer {token}
Content-Type: application/json

{
    "data": "KRM20240101001",
    "size": 200
}
```

**Response:**
```json
{
    "status": "success",
    "data": {
        "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
        "filename": "qr_KRM20240101001.png"
    }
}
```

### Validate QR Code
```http
POST /api/v1/qr/validate
Authorization: Bearer {token}
Content-Type: application/json

{
    "qr_data": "KRM20240101001"
}
```

**Response:**
```json
{
    "status": "success",
    "data": {
        "valid": true,
        "shipment": {
            "id_pengiriman": "KRM20240101001",
            "tanggal": "2024-01-01",
            "status": 1,
            "customer_name": "ASTRA OTOPART"
        }
    }
}
```

## Inventory Management API

### Get Categories
```http
GET /api/v1/categories
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id_kategori": "KTG01",
            "nama": "KATEGORI 1",
            "keterangan": "KATEGORI 1",
            "item_count": 25
        }
    ]
}
```

### Get Items
```http
GET /api/v1/items
Authorization: Bearer {token}
```

**Query Parameters:**
- `category_id` (optional): Filter by category
- `search` (optional): Search by item name
- `page` (optional): Page number
- `limit` (optional): Items per page

**Response:**
```json
{
    "status": "success",
    "data": {
        "items": [
            {
                "id_barang": "BRG0001",
                "nama": "BRAKE SHOE HONDA ASP",
                "satuan": "SATUAN 1",
                "del_no": "Box",
                "kategori": {
                    "id_kategori": "KTG01",
                    "nama": "KATEGORI 1"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 35,
            "total_pages": 2
        }
    }
}
```

## Customer Management API

### Get Customers
```http
GET /api/v1/customers
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id_pelanggan": "CST0001",
            "nama": "ASTRA OTOPART",
            "telepon": "021-4603550",
            "alamat": "jakarta",
            "total_shipments": 15,
            "last_shipment": "2024-01-01"
        }
    ]
}
```

## Courier Management API

### Get Couriers
```http
GET /api/v1/couriers
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id_kurir": "KRR01",
            "nama": "EKO",
            "jenis_kelamin": "Laki-Laki",
            "telepon": "081385195955",
            "alamat": "TANGERANG",
            "active_shipments": 3,
            "total_deliveries": 150
        }
    ]
}
```

## Reporting API

### Get Shipment Reports
```http
GET /api/v1/reports/shipments
Authorization: Bearer {token}
```

**Query Parameters:**
- `date_from` (required): Start date (YYYY-MM-DD)
- `date_to` (required): End date (YYYY-MM-DD)
- `format` (optional): Response format (json, excel, pdf)
- `group_by` (optional): Group by field (customer, courier, status)

**Response:**
```json
{
    "status": "success",
    "data": {
        "summary": {
            "total_shipments": 150,
            "delivered": 140,
            "pending": 8,
            "cancelled": 2,
            "total_items": 1250
        },
        "by_customer": [
            {
                "customer_name": "ASTRA OTOPART",
                "shipment_count": 45,
                "item_count": 380
            }
        ],
        "by_courier": [
            {
                "courier_name": "EKO",
                "shipment_count": 25,
                "delivery_rate": 96.0
            }
        ],
        "daily_breakdown": [
            {
                "date": "2024-01-01",
                "shipments": 5,
                "items": 42
            }
        ]
    }
}
```

### Export Report
```http
GET /api/v1/reports/export
Authorization: Bearer {token}
```

**Query Parameters:**
- `type` (required): Report type (shipments, inventory, customers)
- `format` (required): Export format (excel, pdf)
- `date_from` (optional): Start date filter
- `date_to` (optional): End date filter

**Response:**
```json
{
    "status": "success",
    "data": {
        "download_url": "/downloads/reports/shipments_2024-01-01_2024-01-31.xlsx",
        "filename": "shipments_2024-01-01_2024-01-31.xlsx",
        "expires_at": "2024-01-01T23:59:59Z"
    }
}
```

## Error Handling

### Standard Error Response Format
```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    },
    "code": "ERROR_CODE"
}
```

### HTTP Status Codes

| Status Code | Description |
|-------------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation errors |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

### Common Error Codes

| Error Code | Description |
|------------|-------------|
| AUTH_REQUIRED | Authentication required |
| INVALID_CREDENTIALS | Invalid username or password |
| INSUFFICIENT_PERMISSIONS | User lacks required permissions |
| VALIDATION_FAILED | Request validation failed |
| RESOURCE_NOT_FOUND | Requested resource not found |
| DUPLICATE_ENTRY | Resource already exists |
| RATE_LIMIT_EXCEEDED | Too many requests |

## Rate Limiting

API requests are limited to:
- 1000 requests per hour for authenticated users
- 100 requests per hour for unauthenticated requests
- 10 requests per minute for authentication endpoints

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## Pagination

List endpoints support pagination with the following parameters:
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 20, max: 100)

Pagination information is included in the response:
```json
{
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "total_pages": 8,
        "has_next": true,
        "has_prev": false
    }
}
```

## Webhooks

The system supports webhooks for real-time notifications:

### Shipment Status Updates
```http
POST https://your-webhook-url.com/shipment-status
Content-Type: application/json

{
    "event": "shipment.status_updated",
    "timestamp": "2024-01-01T12:00:00Z",
    "data": {
        "id_pengiriman": "KRM20240101001",
        "old_status": 0,
        "new_status": 1,
        "updated_by": "KRR01"
    }
}
```

### New Shipment Created
```http
POST https://your-webhook-url.com/shipment-created
Content-Type: application/json

{
    "event": "shipment.created",
    "timestamp": "2024-01-01T12:00:00Z",
    "data": {
        "id_pengiriman": "KRM20240101001",
        "customer_id": "CST0001",
        "courier_id": "KRR01",
        "created_by": "USR01"
    }
}
```

## SDK and Integration Examples

### PHP SDK Example
```php
use App\Libraries\LogisticsAPI;

$api = new LogisticsAPI('your-api-token');

// Get shipments
$shipments = $api->getShipments([
    'status' => 1,
    'date_from' => '2024-01-01',
    'date_to' => '2024-01-31'
]);

// Create shipment
$newShipment = $api->createShipment([
    'tanggal' => '2024-01-01',
    'id_pelanggan' => 'CST0001',
    'id_kurir' => 'KRR01',
    'no_kendaraan' => 'B021ZIG',
    'no_po' => 'PO123456',
    'items' => [
        ['id_barang' => 'BRG0001', 'qty' => 5]
    ]
]);
```

### JavaScript/Node.js Example
```javascript
const LogisticsAPI = require('./logistics-api');

const api = new LogisticsAPI('your-api-token');

// Get shipments
const shipments = await api.getShipments({
    status: 1,
    dateFrom: '2024-01-01',
    dateTo: '2024-01-31'
});

// Update shipment status
await api.updateShipmentStatus('KRM20240101001', {
    status: 1,
    penerima: 'John Doe'
});
```

This API documentation provides comprehensive coverage of all available endpoints and their usage patterns for integrating with the logistics system.