[API Sections](../Sections.md)

# Error log - GET

View the log of unexpected HTTP status codes reported by client applications.

## Request

**URL** : `/v3/request/error-log`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
limit | integer | 50 | Limit the collection | limit=50
offset | integer | 0 | Limit offset | offset=0

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "method": "GET",
        "expected_status_code": 200,
        "returned_status_code": 500,
        "request_uri": "/v3/resource-types/3/items",
        "source": "app",
        "created": "2026-09-20 08:14:02",
        "debug": null
    }
]
```
