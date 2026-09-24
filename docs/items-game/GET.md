[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Game) - GET

Return the items collection for the given resource.

The `game` item type is used to track board and dice games, the structure of the item is generic enough to allow tracking of the majority of games without any modification.

It should be noted that game data is not stored in the item, rather, it is stored in a couple of endpoints below item.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
sort | string | | Sort collection | sort=field1:asc|field2:desc
include-players | boolean | false | Optionally include assigned players for each game | include-players=true
complete | int | | Filter by the complete status, 1 for complete, 0 for open, not included for all | complete=1

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "ZPzMWAX3ep",
        "name": "Yahtzee game",
        "description": "Yahtzee game create via the Yahtzee app",
        "game": {
            "scores": [
                {
                    "player_id": "NOXLnvL8vP",
                    "player_name": "Dean",
                    "score": 312
                },
                {
                    "player_id": "3WXLA87eVO",
                    "player_name": "Aideen",
                    "score": 228
                }
            ],
            "winner": {
                "player_id": "NOXLnvL8vP",
                "player_name": "Dean",
                "score": 312
            }
        },
        "statistics": {
            "turns": 0,
            "scores": []
        },
        "winner": {
            "id": "NOXLnvL8vP",
            "name": "Dean"
        },
        "players": [],
        "score": 312,
        "complete": 1,
        "created": "2022-08-29 19:50:08",
        "updated": "2022-08-29 20:05:42"
    },
    {
        "id": "akzpNXd38g",
        "name": "Yahtzee game",
        "description": "Yahtzee game create via the Yahtzee app",
        "game": {
            "scores": [
                {
                    "player_id": "3WXLA87eVO",
                    "player_name": "Aideen",
                    "score": 220
                },
                {
                    "player_id": "NOXLnvL8vP",
                    "player_name": "Dean",
                    "score": 184
                }
            ],
            "winner": {
                "player_id": "3WXLA87eVO",
                "player_name": "Aideen",
                "score": 220
            }
        },
        "statistics": {
            "turns": 0,
            "scores": []
        },
        "winner": {
            "id": "3WXLA87eVO",
            "name": "Aideen"
        },
        "players": [],
        "score": 220,
        "complete": 1,
        "created": "2022-08-14 19:42:50",
        "updated": "2022-08-14 19:59:15"
    },
    ...
]
```
