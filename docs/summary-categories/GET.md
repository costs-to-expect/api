[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Summary (Categories) - GET

Return a count of the categories for a resource type.

## Request

**URL** : `/v3/summary/resource-types/{resource_type_id}/categories`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
search | string | | Search collection fields | search=name:partial_search_term|description:partial_search_term

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "categories": 2
}
```
