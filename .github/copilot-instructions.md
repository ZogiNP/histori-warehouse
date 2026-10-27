# Copilot instructions for this repository

- This workspace currently appears to be an empty PHP/XAMPP web project rooted at `c:\xampp\htdocs\Warehouse`. Treat it as a legacy web app unless more source files appear.
- Prefer plain PHP and Apache-compatible patterns over framework assumptions. Do not introduce Laravel, Symfony, Node, or Composer tooling unless the repo already contains those files.
- Keep code compatible with a local XAMPP environment: PHP + Apache + MySQL/MariaDB, simple static assets, and direct document-root execution.
- Use project-root-relative paths and PHP-safe path helpers (`__DIR__`, `dirname(__FILE__)`, `$_SERVER['DOCUMENT_ROOT']`) instead of hardcoded Windows absolute paths.
- Preserve legacy conventions if files exist: procedural scripts, simple include files, direct HTML in PHP templates, and minimal configuration files are common in this type of repository.
- Keep database access centralized (for example, a config or include file) rather than scattering raw SQL across pages or templates.
- Separate concerns: PHP logic should not be mixed with presentation unless the existing project already does so. When editing, follow the local structure already in use.
- When adding pages or scripts, make them runnable from a browser under XAMPP without requiring a build step or custom runtime installation.
- If a feature touches multiple files, prefer the minimal structural change: add a small include/helper or config hook instead of introducing a new architecture.
- Favor explicit, readable PHP over clever abstractions; local legacy apps usually value stability and straightforward maintenance.
- Do not assume a package manager or CI pipeline exists. Check manually before adding tooling or dependency files.
- Validate changes with the lightest relevant command: `php -l path/to/file.php` for syntax checking, or a browser/manual smoke test when the change affects a page or form.
- For MySQL work, keep SQL simple and compatible with MariaDB/MySQL defaults; avoid database-specific syntax unless the existing code already uses it.
- Prefer small, targeted edits that match surrounding code style rather than broad refactors.
- If the project later grows, keep the architecture simple: config, includes/helpers, pages/controllers, and assets, without adding framework complexity prematurely.
- Use filenames and folder names that match the local convention already present; do not invent new module names or patterns without evidence.
- When unsure, inspect the nearest existing PHP file for local patterns before adding new files or service layers.

This repo has no existing AI instructions or source files to merge from, so the guidance above reflects the actual environment and the typical XAMPP/PHP project assumptions visible in this workspace.
