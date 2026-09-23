[API Sections](Sections.md)

# API - GET

Entry point for the API, shows the meta data for the API and lists all routes and the supported verbs.

## Request

**URL** : `/v3/`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "api": {
        "version": "v3.05.0",
        "prefix": "v3",
        "release_date": "2022-09-11",
        "changelog": {
            "api": "/v3/changelog",
            "markdown": "https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md"
        },
        "readme": "https://github.com/costs-to-expect/api/blob/master/README.md",
        "documentation": "https://postman.costs-to-expect.com/?version=latest"
    },
    "routes": {
        "v3": {
            "methods": [
                "OPTIONS",
                "GET",
                "HEAD"
            ],
            "uri": "/v3"
        },
        ...
    }
}
```
