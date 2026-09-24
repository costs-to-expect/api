[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)

# Account - Request Budget Pro migration

Request that your Budget items be migrated to Budget Pro. This queues a background job rather than migrating immediately.

## Request

**URL** : `/v3/auth/user/migrate/budget-pro/request-migration`

**Method** : `POST`

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "message": "The migration request has been received, a job has been added to the queue to process the request"
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Authentication required, please try again with a Bearer."
}
```
