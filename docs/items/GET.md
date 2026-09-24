[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Items - GET

Return the items collection across every resource within the resource type, rather than for a single resource. Every resource within a resource type shares the same item type, so the fields returned depend on the resource type's item type, in the same way as the per-resource items collection. This example shows the `allocated-expense` shape, each item additionally includes the `resource` it belongs to since that's no longer implied by the URL.

Unlike the per-resource items collection, this endpoint doesn't support `collection=true` to override pagination.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
limit | integer | 10 | Limit the collection | limit=10
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=field1:partial_search_term|field2:partial_search_term
sort | string | | Sort collection | sort=field1:asc|field2:desc
include-categories | boolean | false | Optionally include categories assigned to each item | include-categories=true
include-subcategories | boolean | false | Optionally include subcategories assigned to each item, requires include-categories | include-subcategories=true
include-unpublished | boolean | false | Optionally include unpublished expenses in the collection | include-unpublished=true
category | string | | Filter by category | category=2AP1axw6L7
subcategory | string | | Filter by subcategory, requires category | subcategory=2AP1axw6L7
year | integer | | Filter by year | year=2018
month | integer | | Filter by month | month=1

For a resource type using the `game` item type, the only additional parameter supported is `complete` (boolean), and `search`/`sort` are not supported.

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "aX35eXV3oR",
        "name": "Ice cream",
        "description": null,
        "currency": {
            "id": "epMqeYqPkL",
            "code": "GBP",
            "name": "Sterling"
        },
        "total": "2.50",
        "percentage": 100,
        "actualised_total": "2.50",
        "effective_date": "2022-08-28",
        "categories": [],
        "created": "2022-08-28 11:26:07",
        "updated": null,
        "resource": {
            "id": "Eq9g6BgJL0",
            "name": "Niall",
            "description": "Niall James Blackborough, born on the 22nd April 2019 at 17:46, these are all the expenses we have recorded for him."
        }
    }
]
```
