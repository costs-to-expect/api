[API Sections](../Sections.md)

# Item types - GET

View the available item types.

Item types dictate the type of item that gets added below a resource, item types adjust both the structure of the item object and the functions available.

*Support for new item types needs to be added in the code, we don't allow it to be changed via the API so no POST endpoint*

## Supported item types 

The Costs to Expect API, supports three item types:

### Allocated Expense

Chronological expenses tracking, use by the [Expense](https://app.costs-to-expect.com) App. In addition to defining the data for an expense, you can set a publish after date and allocate a percentage of the total cost. Expenses can be partially transferred to other resources to split the cost and transferred entirely to another resource to move the cost.

### Budget

Budget items are used by the [Budget](https://budget.costs-to-expect.com) App. Budgets items are a definition of a planned expense and the frequency with which it occurs.

### Budget Pro

Budget Pro items are used by the [Budget](https://budget-pro.costs-to-expect.com) App. Budgets items are a definition of a planned expense and the frequency with which it occurs.

### Game

Game items are used by out scoring Apps, [Yahtzee](https://yahtzee.game-scorer.com) and [Yatzy](https://yatzy.game-scorer.com). Game items are specific to the game but typically they are used to record the players, the winner(s) and the final score.

## Request

**URL** : `/v3/item-types/`

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
        "id": "WkxwR04GPo",
        "name": "budget-pro",
        "friendly_name": "Budgeting",
        "description": "Plan your budgets",
        "example": "Annual Personal Budget, Business Budget, Savings plan...",
        "created": "2023-06-01 15:20:43"
    },
    {
        "id": "VezyrJyMlk",
        "name": "budget",
        "friendly_name": "Budgeting",
        "description": "Plan your budgets",
        "example": "Annual Personal Budget, Business Budget, Savings plan...",
        "created": "2022-08-04 15:55:36"
    },
    {
        "id": "2AP1axw6L7",
        "name": "game",
        "friendly_name": "Track a game",
        "description": "Track your board, card and dice game sessions.",
        "example": "Check the item_subtype collection, more added on request",
        "created": "2020-10-08 09:33:25"
    },
    {
        "id": "OqZwKX16bW",
        "name": "allocated-expense",
        "friendly_name": "Create an expense chronological tracker",
        "description": "Track expenses over time, additionally, an expense can be partially allocated to another tracker.",
        "example": "Examples include, the cost to raise a child and start-up expenses for your business.",
        "created": "2019-09-18 12:47:07"
    }
]
```
