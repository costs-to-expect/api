[API Sections](../Sections.md)

# Schemas

We are in the process of generating schemas for all of our data.  This is a work in progress, and we will update this page as we add new schema files.

There are schema files for response data 'objects', and schema files for the entire OPTIONS response.

## Response Objects

The schema files are located within the API repo at `public/api/schema`.

- [Assigned category](https://github.com/costs-to-expect/api/blob/master/public/api/schema/assigned-category.json)
- [Assigned subcategory](https://github.com/costs-to-expect/api/blob/master/public/api/schema/assigned-subcategory.json)
- [Category](https://github.com/costs-to-expect/api/blob/master/public/api/schema/category.json)
- [Currency](https://github.com/costs-to-expect/api/blob/master/public/api/schema/currency.json)
- [Error log](https://github.com/costs-to-expect/api/blob/master/public/api/schema/error-log.json)
- [Item of type Allocated Expense](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-allocated-expense.json)
- [Item category](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-category.json)
- [Item of type Game](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-game.json)
- [Item subcategory](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-subcategory.json)
- [Item subtype](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-subtype.json)
- [Item type](https://github.com/costs-to-expect/api/blob/master/public/api/schema/item-type.json)
- [Partial transfer](https://github.com/costs-to-expect/api/blob/master/public/api/schema/partial-transfer.json)
- [Permitted user](https://github.com/costs-to-expect/api/blob/master/public/api/schema/permitted-user.json)
- [Resource type including permitted users](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource-type-include-permitted-users.json)
- [Resource type including resources](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource-type-include-resources.json)
- [Item of type Allocated Expense as resource type level](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource-type-item-allocated-expense.json)
- [Item of type Game as resource type level](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource-type-item-game.json)
- [Resource type](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource-type.json)
- [Resource](https://github.com/costs-to-expect/api/blob/master/public/api/schema/resource.json)
- [Subcategory](https://github.com/costs-to-expect/api/blob/master/public/api/schema/subcategory.json)
- [Transfer](https://github.com/costs-to-expect/api/blob/master/public/api/schema/transfer.json)

## OPTIONS Response

The schema files for OPTIONS responses are located within the API repo at `public/api/schema/options` and `public/api/schema/auth/options`

We aren't sure why there are two directories for OPTIONS responses schemas, we will sort is out as we add aditional tests.

### OPTIONS 

- [Resource type collection](https://github.com/costs-to-expect/api/blob/master/public/api/schema/options/resource-type-collection.json)
- [Resource type](https://github.com/costs-to-expect/api/blob/master/public/api/schema/options/resource-type.json)

### Authentication OPTIONS

- [Create password](https://github.com/costs-to-expect/api/blob/master/public/api/schema/auth/options/create-password.json)
- [Register](https://github.com/costs-to-expect/api/blob/master/public/api/schema/auth/options/register.json)