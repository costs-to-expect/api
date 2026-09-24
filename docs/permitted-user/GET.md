[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Permitted users](../permitted-users/GET.md)

# Permitted user - GET

Return an individual permitted user.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/permitted-users/{permitted_user_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "3ZY2Kx91mN",
    "user": {
        "id": "d185Q15grY",
        "name": "Jane Doe",
        "email": "jane.doe@example.com"
    },
    "created": "2019-04-07 11:26:10"
}
```
