[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Permitted users - GET

View the users who have been granted access to a resource type, in addition to the resource type owner.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/permitted-users`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 10 | Limit the collection | limit=10
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=name:partial_search_term|email:partial_search_term
sort | string | | Sort collection | sort=name:asc|email:desc

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "3ZY2Kx91mN",
        "user": {
            "id": "d185Q15grY",
            "name": "Jane Doe",
            "email": "jane.doe@example.com"
        },
        "created": "2019-04-07 11:26:10"
    }
]
```
