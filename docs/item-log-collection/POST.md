[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)

# Item Log - POST

Create a new entry in the item log.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
message | string | Yes | The human readable log message
parameters | json | Yes | Data relevant to the message which can be used to construct links/actions etc.

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
 {
    "id": "l8M2Ja1kzE",
    "message": "Scored their Small straight, scoring 30",
    "parameters": {
        "combo": "small_straight",
        "score": 30,
        "player": "3WXLA87eVO",
        "section": "lower"
    },
    "created": "2022-09-06 12:43:13",
    "updated": "2022-09-06 12:43:13"
} "updated": "2022-09-06 12:42:36"
```

### Validation error

**Code** : `422 OK`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "message": {
            "errors": [
                "The message must be a string."
            ]
        },
        "value": {
            "errors": [
                "The parameters should be a valid JSON string."
            ]
        }
    }
}
```