# AGENTS.md

## Build, Lint, and Test Commands

## Commit Guidelines

- **Group related files in a single commit.**
  - Do not commit all files at once if they are unrelated. Make separate commits for logically distinct changes (e.g., database logic vs. UI changes).
- **Write concise, descriptive commit messages** that explain the "why" of the change, not just the "what".
- **Do not include any credit or signature for opencode or any agent in commit messages.**
- **Commit message format:**
  - Short summary (max 72 chars)
  - Blank line
  - Detailed explanation (if needed)
  - Example:
    ```
    Clean up: remove redundant comments from database and login logic

    - Removed all unused and redundant comments from lib/database.php and pages/login.php for clarity and maintainability.
    ```
- **Do not include unrelated or accidental changes in a commit.**
- **Review your staged changes** before committing.

- **Lint all PHP files:**
  ```sh
  find . -name '*.php' -exec php -l {} +
  ```
- **Lint a single file:**
  ```sh
  php -l path/to/file.php
  ```
- **Run a local dev server:**
  ```sh
  php -S localhost:8000 -t public
  ```
- **Testing:**
  - No tests found in this codebase. To add tests, use [PHPUnit](https://phpunit.de/):
    ```sh
    ./vendor/bin/phpunit tests
    ```
  - Install with Composer: `composer require --dev phpunit/phpunit`

## Code Style Guidelines

- **Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards**:
  - 4 spaces per indent, no tabs
  - Max line length: 120 (soft), 80 preferred
  - No trailing whitespace; files end with LF
  - Opening `{` on new line for classes, methods, and control structures
  - One statement per line
- **Imports/Includes:**
  - Use `require_once` for dependencies
  - Place includes at the top of files
- **Naming:**
  - Classes: `StudlyCaps` (PascalCase)
  - Functions/variables: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`
- **Types:**
  - Use type hints and return types where possible
  - Use strict types: `declare(strict_types=1);` at the top
- **Error Handling:**
  - Use exceptions for error cases (e.g., PDOException)
  - Avoid `die()` except for fatal errors
- **Security:**
  - Always escape output (e.g., `htmlspecialchars`)
  - Use prepared statements for DB queries
- **Session Management:**
  - Always start session at the top of entry scripts
- **Formatting:**
  - Blank lines to separate logical blocks
  - No closing `?>` in pure PHP files

_This file is for agentic coding agents. Please update if conventions change._
