[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)
[Category](../category/GET.md)
[Subcategories](../subcategories/GET.md)

# Subcategory - GET

Return an individual subcategory.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "OwAnNrnBvj",
    "name": "Clothes & Food etc.",
    "description": "Those things that nobody can do without.",
    "created": "2018-09-06 22:04:47"
}
```
