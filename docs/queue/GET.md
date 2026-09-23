[API Sections](../Sections.md)

# Queue - GET

Return all the jobs on the queue, response delayed by five minutes.

## Request

**URL** : `/v3/queue/`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "abcde-12345",
        "name": "US Dollar",
        "created": "2020-09-28 10:27:34"
    },
    ...
]
```
