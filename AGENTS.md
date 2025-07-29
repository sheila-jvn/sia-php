# AGENTS.md

## Build, Lint, and Test

- If you need to verify the result of a code, you can use the Playwright tools to do manual integration tests.
  - **Assume the server is already running at** `localhost:8000/$urlPrefix`. If it is not running, request the user to start the server before proceeding with integration tests. (`$urlPrefix` most likely going to be `/sia/public`, but check config.php if it seems to be wrong)

- **Lint all PHP files:**  
  `find . -name '*.php' -exec php -l {} +`
- **Lint a single file:**  
  `php -l path/to/file.php`
- **Run local dev server:**  
  `php -S localhost:8000 -t public`
- **Testing:**  
  _No tests present. To add tests:_  
  `composer require --dev phpunit/phpunit`  
  `./vendor/bin/phpunit tests`

## Commit Guidelines

- Group related changes; do not mix unrelated files.
- Write concise, descriptive commit messages (max 72 chars summary).
- No agent or opencode credit in commits.
- Review staged changes before committing.

## Code Style

- **Standard:** [PSR-12](https://www.php-fig.org/psr/psr-12/)
- 4 spaces per indent, no tabs; max line 120 (soft), 80 preferred.
- Use `require_once` for dependencies; place includes at top.
- Naming: Classes `StudlyCaps`, functions/vars `camelCase`, constants `UPPER_SNAKE_CASE`.
- Use type hints, return types, and `declare(strict_types=1);` at top.
- Use exceptions for errors; avoid `die()` except for fatal errors.
- Escape output (`htmlspecialchars`), use prepared DB statements.
- Start session at top of entry scripts.
- No closing `?>` in pure PHP files.

## UI & Frontend

- **UI Framework:** Bootstrap 5 is used for all user interface components and layout. Refer to the [Bootstrap 5 documentation](https://getbootstrap.com/docs/5.0/getting-started/introduction/) for usage and customization guidelines.
- **Bootstrap Native:** All UI should use Bootstrap classes and [Bootstrap Icons](https://icons.getbootstrap.com/) for consistency. Minimize custom CSS and avoid non-Bootstrap icon libraries. Only add custom styles if absolutely necessary for functionality or clarity.
- **UI Principles:** Use logical button colors that match user expectations—primary for main actions, secondary for neutral actions, success for positive outcomes, warning for caution, and danger for destructive actions.

## Centralized Config Loading

- The application now loads `lib/config.php` automatically for all pages using layouts by requiring it at the top of `pages/_layout.php`.
- This ensures `$urlPrefix` and other config variables are always available in layouts and sidebar links, without manual includes on every page.
- Only pages that need config before layout (e.g., for redirects) should require it manually at the top.

**Test credentials:**

- Username: `admin`
- Password: `admin`
