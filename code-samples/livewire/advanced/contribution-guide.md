# Contribution Guide

**Source URL:** https://livewire.laravel.com/docs/4.x/contribution-guide

## Overview

Welcome to the Livewire contribution guide. This guide covers how you can contribute to Livewire by submitting new features, fixing failing tests, or resolving bugs.

## Setting Up Livewire and Alpine Locally

To contribute, fork and clone both the Livewire and Alpine repositories.

### Fork and Clone Livewire

```bash
# Fork and clone Livewire
gh repo fork livewire/livewire --default-branch-only --clone=true --remote=false -- livewire

# Switch to livewire directory
cd livewire

# Install composer dependencies
composer install

# Configure Dusk
vendor/bin/dusk-updater detect --no-interaction
```

### Fork and Clone Alpine

```bash
# Fork and clone Alpine
gh repo fork alpinejs/alpine --default-branch-only --clone=true --remote=false -- alpine

# Switch to alpine directory
cd alpine

# Install npm dependencies
npm install

# Build all Alpine packages
npm run build

# Link all Alpine packages locally
cd packages/alpinejs && npm link && cd ../../
cd packages/anchor && npm link && cd ../../
cd packages/collapse && npm link && cd ../../
cd packages/csp && npm link && cd ../../
cd packages/docs && npm link && cd ../../
cd packages/focus && npm link && cd ../../
cd packages/history && npm link && cd ../../
cd packages/intersect && npm link && cd ../../
cd packages/mask && npm link && cd ../../
cd packages/morph && npm link && cd ../../
cd packages/navigate && npm link && cd ../../
cd packages/persist && npm link && cd ../../
cd packages/sort && npm link && cd ../../
cd packages/ui && npm link && cd ../../

# Switch back to livewire
cd ../livewire

# Link all packages
npm link alpinejs @alpinejs/anchor @alpinejs/collapse @alpinejs/csp @alpinejs/docs @alpinejs/focus @alpinejs/history @alpinejs/intersect @alpinejs/mask @alpinejs/morph @alpinejs/navigate @alpinejs/persist @alpinejs/sort @alpinejs/ui

# Build Livewire
npm run build
```

## Contributing a Failing Test

If you encounter a bug, the easiest approach is to contribute a failing test. This helps maintainers identify and fix the issue.

### 1. Determine Where to Add Your Test

Livewire core is organized into feature folders:

```
src/Features/SupportAccessingParent
src/Features/SupportAttributes
src/Features/SupportAutoInjectedAssets
src/Features/SupportBladeAttributes
src/Features/SupportChecksumErrorDebugging
src/Features/SupportComputed
src/Features/SupportConsoleCommands
src/Features/SupportDataBinding
// ...
```

Locate a feature folder related to the bug. If unsure, mention it in your pull request.

### 2. Determine the Test Type

**Unit tests:** Focus on PHP implementation

```php
use Tests\TestCase;

class UnitTest extends TestCase
{
    public function test_livewire_can_run_action(): void
    {
       // ...
    }
}
```

**Browser tests:** Run in a real browser, focus on JavaScript

```php
use Tests\BrowserTestCase;

class BrowserTest extends BrowserTestCase
{
    public function test_livewire_can_run_action()
    {
        // ...
    }
}
```

### 3. Running Tests

```bash
# Run a specific test
vendor/bin/phpunit --filter "test_can_make_method_a_computed"

# Run all tests
vendor/bin/phpunit
```

For headless mode, create `.env` with:
```
DUSK_HEADLESS_DISABLED=false
```

### 4. Preparing Your Pull Request Branch

```bash
# Create a new branch
git checkout -b my-feature

# Stage and commit changes
git add .
git commit -m "Add my feature"

# Push to your fork
git push origin my-feature
```

### 5. Submitting Your Pull Request

1. Navigate to your forked repository on GitHub
2. Click "Compare & pull request"
3. Fill out the pull request template with:
   - Discussion link (if applicable)
   - Branch name confirmation
   - Whether changes are related
   - Test inclusion confirmation
   - Thorough description of improvements

4. Click "Create pull request"

Maintainers will review and may request changes. Address feedback promptly.

Thank you for contributing to Livewire!

## See Also

- [Security](./security.md) — Security best practices
- [GitHub Repository](https://github.com/livewire/livewire) — Livewire on GitHub
