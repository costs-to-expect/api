[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Budget Pro) - GET

Return the items collection for the given resource.

The `budget-pro` item type is used to define items that appear on a budget in Budget Pro. The object is a definition of the item and the frequency with which it repeats.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
sort | string | | Sort collection | sort=field1:asc|field2:desc

## Response

**Code** : `200 OK`

**Content** : 
```json
[
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
        "deleted": false,
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
    },
    ...
]
```
