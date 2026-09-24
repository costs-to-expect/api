[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Game) - POST

Create a new game, the table below details the fields and their data type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the game
description | string | No | A optional description of the expense

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "ZPzMWAX3ep",
    "name": "Yahtzee game",
    "description": "Yahtzee game create via the Yahtzee app",
    "game": {
        "scores": [],
        "winner": null
    },
    "statistics": {
        "turns": 0,
        "scores": []
    },
    "winner": null,
    "players": [],
    "score": null,
    "complete": 0,
    "created": "2022-08-29 19:50:08",
    "updated": "2022-08-29 20:05:42"
},
```

### Validation error

**Code** : `422 OK`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "name": {
            "errors": [
                "The name must be a string."
            ]
        }
    }
}
```