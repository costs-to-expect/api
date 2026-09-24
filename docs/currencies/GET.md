[API Sections](../Sections.md)

# Currencies - GET

Return the supported currencies.

*Support for new currencies needs to be added in the code, we don't allow it to be changed via the API so no POST endpoint*

## Request

**URL** : `/v3/currencies/`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=field1:partial_search_term|field2:partial_search_term
sort | string | | Sort collection | sort=field1:asc|field2:desc


## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "OZWwOgwAmV",
        "code": "USD",
        "name": "US Dollar",
        "created": "2020-09-28 10:27:34"
    },
    {
        "id": "epMqeYqPkL",
        "code": "GBP",
        "name": "Sterling",
        "created": "2020-09-28 10:27:34"
    },
    {
        "id": "96pw2pv4ZG",
        "code": "EUR",
        "name": "Euro",
        "created": "2020-09-28 10:27:34"
    }
]
```
