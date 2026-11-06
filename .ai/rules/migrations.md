---
paths:
  - 'database/migrations/**'
---

# Migrations

## Base schema in raw SQL, changes via Schema builder
The full initial schema is created with raw SQL DB::statement calls in one migration; add later changes through incremental Schema::create/Schema::table migrations.

## Store statuses as plain strings
Store status values as plain strings and compare them to literal values such as Pending, Published, and In Progress; do not introduce PHP enums or model constants for statuses.
