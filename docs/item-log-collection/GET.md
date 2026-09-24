[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)

# Item Log - GET

Below each item we support a log, this allows you to store a message and parameters which can be used to 
build a dynamic version of the message or any other feature, such as an undo action.

We use the item log collection to store player moves for the `game` item type.

**Log messages are immutable, once created they cannot be updated or deleted.**

*Support for the allocated-expense and budget item types is coming soon.*

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
[
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
    },
    {
        "id": "z1ALlKM2Y5",
        "message": "Scored their Yahtzee, scoring 0",
        "parameters": {
            "combo": "yahtzee",
            "score": 0,
            "player": "Za4Jb370vl",
            "section": "lower"
        },
        "created": "2022-09-06 12:42:36",
        "updated": "2022-09-06 12:42:36"
    },
    {
        "id": "Ked2dqlLa0",
        "message": "Scored their Full house, scoring 0",
        "parameters": {
            "combo": "full_house",
            "score": 0,
            "player": "Za4Jb370vl",
            "section": "lower"
        },
        "created": "2022-09-06 12:41:59",
        "updated": "2022-09-06 12:41:59"
    },
    ...
]
```
