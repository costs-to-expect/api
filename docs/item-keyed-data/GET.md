[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)
[Keyed Data](../item-keyed-data-collection/GET.md)

# Keyed Data - GET

Return the requested keyed data.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "key": "Za4Jb370vl",
    "value": {
        "score": {
            "bonus": 35,
            "lower": 104,
            "total": 213,
            "upper": 74
        },
        "lower-section": {
            "chance": 16,
            "yahtzee": 0,
            "full_house": 0,
            "four_of_a_kind": 0,
            "large_straight": 40,
            "small_straight": 30,
            "three_of_a_kind": 18
        },
        "upper-section": {
            "ones": 2,
            "twos": 6,
            "fives": 20,
            "fours": 16,
            "sixes": 18,
            "threes": 12
        }
    },
    "created": "2022-09-06 12:19:15",
    "updated": "2022-09-06 12:42:36"
}
```
