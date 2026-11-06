---
paths:
  - 'app/**'
---

# App

## Store statuses as plain strings
Store status values as plain strings and compare them to literal values such as Pending, Published, and In Progress; do not introduce PHP enums or model constants for statuses.

## Use Asia/Makassar for business dates
Use now('Asia/Makassar') for business date comparisons and date-based identifiers.
