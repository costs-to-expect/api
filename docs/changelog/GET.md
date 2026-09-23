[API Sections](../Sections.md)

# Changelog - GET

In addition to providing a changelog file, the changelog route returns all our releases as an easily parsable JSON object. We parse the CHANGELOG.md file to generate the endpoint data so it is always upto date.

## Request

**URL** : `/v3/changelog`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "releases": [
        {
            "release": "[v3.05.0] - 2022-09-11",
            "added": [
                "Added full support for the `budget` item type, all endpoints."
            ],
            "changed": [
                "Updated the landing page image for the 'Budget' App.",
                "Updated the descriptions for all `item` OPTIONS endpoint responses, the descriptions are now specific to the item type`.",
                "Updated the README, added additional Apps and improved the content in general."
            ],
            "fixed": [
                "Corrected PATCH and POST validation for `items`, execution will stop and return immediately.",
                "Corrected an indentation issue in the CHANGELOG."
            ]
        },
        ...
    ]
}
```
