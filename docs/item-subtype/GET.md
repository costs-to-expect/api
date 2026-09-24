[API Sections](../Sections.md)
[Item types](../item-types/GET.md)
[Item type](../item-type/GET.md)
[Item subtypes](../item-subtypes/GET.md)

# Item subtype - GET

Return an individual item subtype.

## Request

**URL** : `/v3/item-types/{item_type_id}/item-subtypes/{item_subtype_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "3JgkeMkB4q",
    "name": "yahtzee",
    "friendly_name": "Yahtzee",
    "description": "Track your Yahtzee games, wins and losses",
    "created": "2022-07-06 12:42:47"
}
```
