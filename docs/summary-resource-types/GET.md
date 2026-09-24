[API Sections](../Sections.md)

# Summary (Resource types) - GET

Return a count of the resource types visible to you.

## Request

**URL** : `/v3/summary/resource-types`

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
    "resource_types": 2
}
```
