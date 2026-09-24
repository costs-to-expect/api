[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Budget) - GET](../items-budget/GET.md)

# Item (Budget) - GET

Return an individual budget item definition.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "OkzK5yp36b",
    "name": "Council Tax",
    "account": "ebb5c735-0308-4e3c-9aea-8a270aebfe15",
    "target_account": null,
    "description": "Council tax for the year",
    "amount": "163.00",
    "currency": {
        "id": "epMqeYqPkL",
        "name": "Sterling",
        "code": "GBP",
        "uri": "/v3/currencies/epMqeYqPkL"
    },
    "category": "fixed",
    "start_date": "2022-04-01",
    "end_date": "2023-03-21",
    "disabled": false,
    "frequency": {
        "day": 10,
        "type": "monthly",
        "exclusions": [
            2,
            3
        ]
    },
    "created": "2022-09-22 21:17:14",
    "updated": null
}
```
