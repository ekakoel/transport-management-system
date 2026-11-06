---
paths:
  - 'routes/**'
---

# Routes

## Role-based authorization in middleware and controllers
Protect route groups with the role: middleware and enforce ownership/state with inline abort_unless checks; do not add Policies or Gates.

## Define explicit named routes
Register each action as an explicit HTTP-verb route with a name, grouping shared prefix and middleware; do not use Route::resource or invokable controllers.
