
# Laravel TMS - Project Instructions

## Project
Transport Management System built with Laravel.

## Core Rules
- Always inspect existing implementation before coding.
- Follow the current Laravel version and existing architecture.
- Do not introduce a new architecture without approval.
- Reuse existing migrations, models, controllers and services.
- Keep database structure consistent with existing migrations.
- Do not create duplicate functionality.
- Do not modify unrelated files.

## Laravel Development
- Follow Laravel conventions.
- Use Eloquent relationships where appropriate.
- Use Form Requests for complex validation.
- Keep controllers focused.
- Use services only when justified by existing architecture.
- Use named routes.
- Follow existing Blade and CSS conventions.
- Avoid unnecessary JavaScript dependencies.

## Database
- Inspect migrations and models before changing database logic.
- Never assume columns or relationships exist.
- Do not remove existing columns or tables without approval.
- Create migrations for approved schema changes.
- Check foreign keys and relationships.

## Development Workflow
1. Understand the requested feature.
2. Inspect only relevant existing files.
3. Identify dependencies and affected components.
4. Explain the implementation briefly.
5. Implement the requested changes.
6. Run targeted verification.
7. Report modified files and verification results.

## Token Optimization
- Never read the entire repository by default.
- Never dump complete files when a targeted section is sufficient.
- Search for specific classes, methods and routes.
- Read related files only when necessary.
- Avoid repeating previous findings.
- Do not regenerate code that already exists.

## Task Memory
For ongoing tasks, consult docs/AI_HANDOFF.md when necessary.
Do not read it automatically for unrelated tasks.
Update it when explicitly requested or when completing a significant milestone.
