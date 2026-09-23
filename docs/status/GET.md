[API Sections](../Sections.md)

# Status - GET

A simple status endpoint, showing the running environment and whether response caching is enabled. For version and release information see the [API entry point](../GET.md).

## Request

**URL** : `/v3/status`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "environment": "production",
    "cache": true
}
```
