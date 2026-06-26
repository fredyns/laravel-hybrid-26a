# CSP

**Source URL:** https://livewire.laravel.com/docs/4.x/csp

## Overview

Livewire offers a CSP-safe build that allows you to use Livewire applications in environments with strict Content Security Policy (CSP) headers that prohibit `'unsafe-eval'`.

## What is Content Security Policy (CSP)?

Content Security Policy (CSP) is a security standard that helps prevent various types of attacks, including Cross-Site Scripting (XSS) and code injection attacks. CSP works by allowing web developers to control which resources the browser is allowed to load and execute.

One of the most restrictive CSP directives is `'unsafe-eval'`, which when omitted, prevents JavaScript from executing dynamic code through functions like `eval()`, `new Function()`, and similar constructs.

## Why CSP Affects Livewire

By default, Livewire uses `new Function()` declarations to compile and execute JavaScript expressions from HTML attributes:

```html
<button wire:click="$set('count', count + 1)">Increment</button>
<div wire:show="user.role === 'admin'">Admin panel</div>
```

While this approach is faster and safer than `eval()`, it still violates the `'unsafe-eval'` CSP directive.

## Enabling CSP-Safe Mode

In your `config/livewire.php` file, set the `csp_safe` option to true:

```php
'csp_safe' => true,
```

**Important:** When you enable CSP-safe mode in Livewire, it also affects all Alpine.js functionality in your application. Alpine will use its CSP-safe evaluator, which means all Alpine expressions will be subject to the same parsing limitations.

## What's Supported

### Basic Livewire Expressions

```html
<button wire:click="increment">+</button>
<button wire:click="decrement">-</button>
<input wire:model="name">
<input wire:model.live="search">
```

### Method Calls with Parameters

```html
<button wire:click="updateUser('John', 25)">Update User</button>
<button wire:click="setCount(42)">Set Count</button>
<button wire:click="saveData({ name: 'John', age: 30 })">Save Object</button>
```

### Property Access and Updates

```html
<input wire:model="user.name">
<input wire:model="settings.theme">
<button wire:click="$set('user.active', true)">Activate</button>
<div wire:show="user.role === 'admin'">Admin Panel</div>
```

### Basic Expressions in Alpine

```html
<div x-data="{ count: 0, name: 'Livewire' }" wire:ignore>
    <button x-on:click="count++">Increment</button>
    <span x-text="count"></span>
    <span x-text="'Hello ' + name"></span>
    <div x-show="count > 5">Count is high!</div>
</div>
```

## What's Not Supported

### Complex JavaScript Expressions

```html
<button wire:click="items.filter(i => i.active).length">Count Active</button>
<div wire:show="users.some(u => u.role === 'admin')">Has Admin</div>
```

### Template Literals and Advanced Syntax

```html
<div x-text="`Hello ${name}`">Bad</div>
<div x-data="{ ...defaults }">Bad</div>
```

### Dynamic Property Access

```html
<div wire:show="user[dynamicProperty]">Bad</div>
<button wire:click="this[methodName]()">Bad</button>
```

## Working Around Limitations

For complex Alpine expressions, use `Alpine.data()` or move logic to methods:

```html
<div x-data="users">
    <div x-show="hasActiveAdmins">Admin panel available</div>
    <span x-text="activeUserCount">0</span>
</div>

<script nonce="[nonce]">
    Alpine.data('users', () => ({
        users: [...],

        get hasActiveAdmins() {
            return this.users.filter(u => u.active && u.role === 'admin').length > 0;
        },

        get activeUserCount() {
            return this.users.filter(u => u.active).length;
        }
    }));
</script>
```

## CSP Headers Example

```
Content-Security-Policy: default-src 'self';
                        script-src 'nonce-[random]' 'strict-dynamic';
                        style-src 'self' 'unsafe-inline';
```

Key points:
- Remove `'unsafe-eval'` from your `script-src` directive
- Use nonce-based script loading with `'nonce-[random]'`
- Consider adding `'strict-dynamic'` for better compatibility

## Performance Considerations

The CSP-safe build uses a different expression evaluator that:
- **Parsing:** Slightly slower initial parsing (usually negligible)
- **Runtime:** Similar runtime performance for simple expressions
- **Bundle size:** Slightly larger JavaScript bundle due to the custom parser

## When to Use CSP-Safe Mode

Consider using CSP-safe mode when:
- Your application requires strict CSP compliance
- You're building applications for security-sensitive environments
- Your organization's security policies prohibit `'unsafe-eval'`
- You're deploying to platforms with mandatory CSP restrictions

## See Also

- [Security](./security.md) — Security best practices
- [Alpine](../features/alpine.md) — Alpine.js integration
