# AGENTS.md

**Database Structure:**
Refer to `sia_php.sql` in the project root for the full database schema, table structures, and sample data.

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

- **UI Framework:** Tailwind CSS v4 is used for all user interface components and layout. The Tailwind configuration (theme/colors) is defined in `pages/_layouts/base.php`. Refer to the [Tailwind CSS documentation](https://tailwindcss.com/docs) for usage and customization guidelines.
- Use the custom theme color classes defined in Tailwind config (see `pages/_layouts/base.php`), such as `bg-primary-600`, `text-primary-700`, `bg-accent-500`, `bg-secondary-100`, `text-error-600`, etc.
- **Icons:** Use [Iconify](https://iconify.design/docs/) for all icons. Avoid other icon libraries unless absolutely necessary for functionality or clarity.
    
### Iconify Usage Example

To use an icon with Iconify, add the `<iconify-icon>` tag with the desired icon name and optional size attributes:

```html
<iconify-icon icon="cil:locomotive" height="36"></iconify-icon>
<iconify-icon icon="cil:paper-plane" width="36"></iconify-icon>
<iconify-icon
   icon="cil:truck"
   style="font-size: 18px"
   height="2em"
></iconify-icon>
```

See the [Iconify documentation](https://iconify.design/docs/) for more options and icon sets.

## Centralized Config Loading

- The application now loads `lib/config.php` automatically for all pages using layouts by requiring it at the top of `pages/_layout.php`.
- This ensures `$urlPrefix` and other config variables are always available in layouts and sidebar links, without manual includes on every page.
- Only pages that need config before layout (e.g., for redirects) should require it manually at the top.

**Test credentials:**

- Username: `admin`
- Password: `admin`
