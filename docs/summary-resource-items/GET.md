[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Summary (Resource items) - GET

Return a summary of the items for a single resource. The shape of the response depends on the resource type's item type, and on which grouping parameters are supplied, in the same way as the [resource type items summary](../summary-items/GET.md), minus the ability to group by resource, since the summary is already scoped to one.

## Request

**URL** : `/v3/summary/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-unpublished | boolean | false | Include unpublished expenses in the summary | include-unpublished=true
years | boolean | false | Group the summary by year | years=true
year | integer | | Summarise a single year, add months=true or month=MM to break it down further | year=2018
months | boolean | false | Group a single year's summary by month, requires year | months=true
month | integer | | Summarise a single month, requires year | month=6
categories | boolean | false | Group the summary by category | categories=true
category | string | | Summarise a single category, add subcategories=true or subcategory=&lt;id&gt; to break it down further | category=98WLap7Bx3
subcategories | boolean | false | Group a single category's summary by subcategory, requires category | subcategories=true
subcategory | string | | Summarise a single subcategory, requires category | subcategory=bqNzrjz1W5
search | string | | Search collection fields | search=name:partial_search_term
filter | string | | Filter the collection | filter=total:10.00:100.00

For a resource type using the `game` item type, the only parameter is `complete` (boolean); the response is shaped differently, see below.

## Response

**Code** : `200 OK`

**Content** (no parameters, `allocated-expense`) : 
```json
[
    {
        "currency": {
            "code": "GBP"
        },
        "count": 2,
        "subtotal": "150.00"
    }
]
```

**Content** (`?year=2018&month=6`, `allocated-expense`) : 
```json
{
    "month": "June",
    "subtotals": [
        {
            "currency": {
                "code": "GBP"
            },
            "count": 2,
            "subtotal": "125.00"
        }
    ]
}
```

**Content** (no parameters, `game`) : 
```json
[
    {
        "resource": {
            "id": "Eq9g6BgJL0",
            "name": "Niall",
            "description": "Niall James Blackborough",
            "item_subtype": {
                "id": "a56kbWV82n",
                "name": "Yahtzee",
                "description": "Standard Yahtzee rules"
            }
        },
        "count": 2
    }
]
```
