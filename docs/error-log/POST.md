[API Sections](../Sections.md)

# Error log - POST

Record that a client application received an unexpected HTTP status code from the API, the table below details the fields and their data type.

## Request

**URL** : `/v3/request/error-log`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
method | string | Yes | The HTTP verb used for the request
expected_status_code | integer | Yes | The status code the client expected
returned_status_code | integer | Yes | The status code the API actually returned
request_uri | string | Yes | The URI that returned the unexpected status code
source | string | Yes | Where the request originated, one of website, api, app, legacy, postman
debug | string | No | Additional debug data

## Responses

### Success

**Code** : `204`

**No Content**

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "source": {
            "errors": [
                "The selected source is invalid."
            ]
        }
    }
}
```
