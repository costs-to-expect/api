[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)
[Log](../item-log-collection/GET.md)

# Keyed Data - GET

Return the requested log item.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{log_id}`

**Method** : `GET`

**Parameters** : None

## Response

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
}
```
