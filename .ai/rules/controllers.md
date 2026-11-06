---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Validate inline in controllers
Validate request input inline with $request->validate() inside controller actions; this project does not use Form Request classes.

## Keep business logic in controllers
Controllers own validation, Eloquent queries, transactions, and business rules directly; there is no Actions, Services, Repositories, or Query-object layer.
