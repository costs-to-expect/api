[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Game) - GET](../items-game/GET.md)

# Item (Game) - GET

Return an individual game.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `GET`

**Parameters** : 

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-players | boolean |  | Optionally include assigned players | include-categories=true

## Response

**Code** : `200 OK`

**Content** : 
```json
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
}
```
