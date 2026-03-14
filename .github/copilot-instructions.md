# Project Guidelines

## Code Style
- Follow existing Laravel + Blade patterns in this repository instead of generic Laravel scaffolds.
- Keep controller methods thin when possible; business logic typically lives in service classes under app/Services.
- Match the current naming and response style used by services, including tuple-like returns such as [status_code, status_message, payload].
- Preserve existing Blade structure and layout splits between admin and driver UIs.
- Prefer small, targeted edits; do not reformat unrelated files.

## Architecture
- Main HTTP routes are in routes/web.php and mostly use resource controllers plus targeted custom endpoints.
- Backend/admin views are under resources/views/backend/* and driver/mobile-style views are under resources/views/driver/*.
- Controllers are under app/Http/Controllers and commonly call service classes (app/Services) for domain workflows.
- Models in app/Models contain Eloquent relations and many domain-specific query or mutation helper methods.
- Role and permission checks use Spatie middleware aliases configured in bootstrap/app.php.

## Build and Test
- Install backend dependencies: composer install
- Install frontend dependencies: npm install
- One-step project bootstrap: composer setup
- Run app + queue + logs + vite concurrently: composer dev
- Run tests: composer test
- Alternative direct test command: php artisan test
- Build frontend assets: npm run build

## Conventions
- Financial and stock workflows (sales, purchases, returns, cash close, driver closing) often use database transactions; keep related writes atomic.
- Many controllers gate actions via permission middleware strings like PermissionName view/create/edit/destroy. Preserve this pattern when adding actions.
- Driver flows frequently branch by authenticated role and render driver.* views; admin flows render backend.* views.
- Existing backend layout references static assets in public/build/backend, while Vite builds resources/css/app.css and resources/js/app.js. Do not migrate asset loading patterns unless explicitly requested.
- Validate assumptions against current code paths before broad refactors; this codebase includes domain-specific behavior beyond Laravel defaults.

## Environment Notes
- .env.example defaults to sqlite and uses database-backed session/cache/queue drivers; required tables must exist locally.
- composer setup runs migrations; ensure local DB configuration is correct before executing it.
- On Windows + Laragon environments, prefer commands already defined in composer scripts to avoid shell-specific differences.
