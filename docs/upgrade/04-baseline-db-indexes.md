# Baseline DB Index Audit

Status: pending local database bootstrap.

Planned scope:

- enumerate all tables
- enumerate all indexes
- detect duplicate indexes
- detect left-prefix overlap
- map heavy query filters and joins
- compare `EXPLAIN` before and after changes

Current blocker:

- No local database configured yet.
- No local dump attached yet.
