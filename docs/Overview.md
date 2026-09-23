# Costs to Expect API: Documentation

## Self-documenting

We have tried to make our API as easy to use as possible, although our documentation is thorough, our goal is you don't read it, once you have fetched the response for the root of the API, you should be able to get at anything you need.

## OPTIONS requests

We have added an OPTIONS endpoint for every route, the OPTIONS request response will list the endpoints supported by the route, any parameters and fields as well as a general overview of the purpose.

## Summaries

If a summary route exists for a collection, the summary route will always by the route with a `/summary/` prefix. A summary route will support all the same parameters as the collection it summaries and possibly a few more.

## Cache

All collections and summaries are cached, if you would like to override the cache and fetch fresh data, include the `X-Skip-Cache` header, The value of the header does not matter, just its existence.

## Public data

The Costs to Expect API includes public data, if you don't want to see any public data, include `exclude-public=1` in any request URIs for resource types.

## Responses

* On success, collections will return an array and a 200.
* On success, show requests will return a single object and a 200, no response envelope.
* Successful POST requests will return a single object and a 201, there are minor exceptions where we return a 204.
* Successful PATCH requests will return 204.
* Successful DELETE requests will return a 204.
* Non 2xx results will return an object with a message field and optionally a fields array. When we 
return a validation error, the response will be 422 and include a fields array which will contain all validation errors.

## Caching

The API caches responses per authenticated user and also a public cache.

You can skip reading from the cache for a specific request by adding the `X-Skip-Cache` header, any value will do, 
the existence of the header is all that matters.

Cache is cleared when we detect a change to relevant data, currently, there is a minor delay before cache is cleared, 
we are planning to clear cache immediately, we want to review the performance impact before making the change.

## Headers

Responses will include multiple headers, the table below details the intention behind each of our custom headers.

| Header          | Purpose                                             |
|:----------------|:----------------------------------------------------|
| X-Total-Count   | Pagination: Total number of results for the request |
| X-Count         | Pagination: Number of results returned              |
| X-Limit         | Pagination: Limit value applied to request          |
| X-Offset        | Pagination: Offset value applied to request         |
| X-Link-Previous | Pagination: URI for previous result set if relevant |
| X-Link-Next     | Pagination: URI for next result set if relevant     |
| X-Last-Updated  | The last time the collection was updated            |
| X-Sort          | Sort options applied to the request                 |
| X-Search        | Search options applied to the request               |
| X-Skip-Cache    | Return collection, bypassing the cache              |
| X-Parameters    | Request parameters applied to request               |
| X-Filter        | Filter options applied to the request               |
