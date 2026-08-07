# .ai/rules index

Maps file globs to the rule files that govern them. Before creating or editing a file, read every rule file whose globs cover it.

| Globs | Rule file |
|---|---|
| `app/Features/**` | [Vertical-Slice-Architecture.md](Vertical-Slice-Architecture.md) |
| `app/Providers/FeatureServiceProvider.php` | [Vertical-Slice-Architecture.md](Vertical-Slice-Architecture.md) |
| `tests/Feature/**` | [Vertical-Slice-Architecture.md](Vertical-Slice-Architecture.md) |
| `routes/web.php`, `routes/api.php` | [Vertical-Slice-Architecture.md](Vertical-Slice-Architecture.md) — feature routes live in colocated `web-routes.php` / `api-routes.php`, not here |
| `vite.config.js`, `resources/css/app.css` | [Vertical-Slice-Architecture.md](Vertical-Slice-Architecture.md) — feature asset globbing and Tailwind `@source` |
