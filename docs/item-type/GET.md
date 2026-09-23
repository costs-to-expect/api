[API Sections](../Sections.md)
[Item types](../item-types/GET.md)

# Item type - GET

Return an individual item type.

## Request

**URL** : `/v3/item-types/{item_type_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "VezyrJyMlk",
    "name": "budget",
    "friendly_name": "Budgeting",
    "description": "Plan your budgets",
    "example": "Annual Personal Budget, Business Budget, Savings plan...",
    "created": "2022-08-04 15:55:36"
}
```
