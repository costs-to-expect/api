[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Summary (Items) - GET

Return a summary of the items across every resource within a resource type. The shape of the response depends on the resource type's item type, and on which grouping parameters are supplied. This example shows the `allocated-expense` shape, showing a subtotal per currency; add `years`, `categories` or `resources` to group the totals, or `year`/`category` to summarise a single one.

## Request

**URL** : `/v3/summary/resource-types/{resource_type_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-unpublished | boolean | false | Include unpublished expenses in the summary | include-unpublished=true
resources | boolean | false | Group the summary by resource | resources=true
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

For a resource type using the `game` item type, the parameters are `resources` (boolean, group by resource) and `complete` (boolean); the response is shaped differently, see below.

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

**Content** (`?categories=true`, `allocated-expense`) : 
```json
[
    {
        "id": "98WLap7Bx3",
        "name": "Essential",
        "description": "The costs that we see as absolutely necessary in raising a child.",
        "subtotals": [
            {
                "currency": {
                    "code": "GBP"
                },
                "count": 1,
                "subtotal": "100.00"
            }
        ]
    }
]
```

**Content** (no parameters, `game`) : 
```json
{
    "resource_type": {
        "id": "d185Q15grY",
        "name": "The Blackborough boys",
        "description": "Family Yahtzee nights"
    },
    "count": 4
}
```

**Content** (`?resources=true`, `game`) : 
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
