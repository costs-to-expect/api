[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Game) - GET](../items-allocated-expense/GET.md)

# Item (Game) - PATCH

Update the game, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
game | json | Game details
statistics | json | Game statistics
winner_id | string | The game winner
score | integer | Final score
complete | boolean | True/1 if the game is complete

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Game can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Game can't be found or is not accessible to you."
}
```

### No body

**Code** : `400`

**Content** : 
```json
{
    "message": "Unable to handle your request, please include a request body."
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "name": {
            "errors": [
                "The game value must be valid json"
            ]
        }
    }
}
```
