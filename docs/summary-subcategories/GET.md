[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)
[Category](../category/GET.md)

# Summary (Subcategories) - GET

Return a count of the subcategories for a category.

## Request

**URL** : `/v3/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories`

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
    "subcategories": 2
}
```
