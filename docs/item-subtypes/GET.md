[API Sections](../Sections.md)
[Item types](../item-types/GET.md)
[Item type](../item-type/GET.md)

# Item subtypes - GET

View the available item subsypes.

Item subtypes can be used to further different items, presently we use the subtypes to differentiate different games, later we will be using subtypes to control features, for example the `budget-pro` subtype will allow additional fields and options.

*Support for new item subtypes needs to be added in the code, we don't allow it to be changed via the API so no POST endpoint*

The default item subtypes for each item type is default, presently we only have different item subtypes for the "game" item type

## Request

**URL** : `/v3/item-types/{item_type_id}/item-subtypes`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=field1:partial_search_term|field2:partial_search_term
sort | string | | Sort collection | sort=field1:asc|field2:desc

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "OZYlY5lbPJ",
        "name": "yatzy",
        "friendly_name": "Yatzy",
        "description": "Track your Yatzy games, wins and losses",
        "created": "2022-08-27 15:26:51"
    },
    {
        "id": "3JgkeMkB4q",
        "name": "yahtzee",
        "friendly_name": "Yahtzee",
        "description": "Track your Yahtzee games, wins and losses",
        "created": "2022-07-06 12:42:47"
    },
    {
        "id": "Q54VLRvNeD",
        "name": "carcassonne",
        "friendly_name": "Carcassonne board games",
        "description": "Track your Carcassonne games, wins and losses",
        "created": "2020-10-08 09:33:25"
    },
    {
        "id": "J65VwRkjLo",
        "name": "scrabble",
        "friendly_name": "Scrabble board games",
        "description": "Track your Scrabble games, wins and losses",
        "created": "2020-10-08 09:33:25"
    }
]
```
