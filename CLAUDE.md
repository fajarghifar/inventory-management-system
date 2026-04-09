# Inventory Management GM System — Claude.md

## Project Overview

Enterprise-grade inventory management system built with **Laravel 12** and **Livewire 3**.

Key features: Point-of-Sale (POS), inventory tracking, purchase orders, double-entry financial ledger, multi-currency support, and data export.

---

## Quick Start

```bash
# Full one-time setup (install deps, migrate DB, build assets)
composer run setup

# Start all dev services (Laravel, Queue, Logs, Vite)
composer run dev
```

Access at `http://localhost:8000` — Default login: **admin / password**

---

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM

---

## Step-by-Step Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 3. Run migrations and seed the database
php artisan migrate:fresh --seed

# 4. Link public storage
php artisan storage:link

# 5. Install and build frontend assets
npm install
npm run build
```

### Using MySQL instead of SQLite

Edit `.env` and update the database section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## Running the Project

```bash
# Development (all services concurrently)
composer run dev

# Or manually:
php artisan serve      # http://localhost:8000
npm run dev            # Vite dev server with hot reload
```

```bash
# Production build
npm run build
php artisan serve
```

---

## Common Commands

### Artisan

```bash
php artisan serve                  # Start local dev server
php artisan migrate:fresh --seed   # Reset and re-seed database
php artisan migrate                # Run pending migrations
php artisan tinker                 # Laravel REPL
php artisan pail                   # Tail application logs
php artisan pint                   # Format code (Laravel Pint)
php artisan queue:listen           # Start queue worker
php artisan storage:link           # Link public storage
```

### Composer

```bash
composer install     # Install PHP dependencies
composer run setup   # Full initial setup
composer run dev     # Start all dev services
composer run test    # Run PHPUnit tests
```

### NPM

```bash
npm install       # Install frontend dependencies
npm run dev       # Dev server with file watching
npm run build     # Production build (output: public/build/)
```

---

## Testing

```bash
composer run test
# or
php artisan test
```

- Framework: **PHPUnit 11.5**
- Config: `phpunit.xml`
- Uses **SQLite in-memory** database for tests
- Unit tests: `tests/Unit/`
- Feature tests: `tests/Feature/`

---

## Project Structure

```
app/
├── Console/          # Artisan commands
├── DTOs/             # Data Transfer Objects
├── Enums/            # PHP Enums
├── Exceptions/       # Custom exceptions
├── Helpers/          # CurrencyHelper.php (global @money() directive)
├── Http/
│   ├── Controllers/  # Web controllers + Api/ (AJAX endpoints)
│   └── Requests/     # Form validation requests
├── Livewire/         # Livewire components (one folder per module)
├── Models/           # Eloquent models (13 models)
├── Providers/        # Service providers
├── Services/         # Business logic services
└── View/             # View composers

database/
├── migrations/       # 18 migration files
├── factories/        # Model factories
└── seeders/          # Database seeders

resources/
├── views/            # Blade templates (one folder per module)
├── css/app.css       # Tailwind CSS entry
└── js/app.js         # JavaScript entry

routes/
├── web.php           # Main web routes
└── auth.php          # Authentication routes
```

---

## Architecture Patterns

### Livewire Components
All UI interactivity is handled via Livewire. Components live in `app/Livewire/` with corresponding Blade views in `resources/views/livewire/`.

Modules: `Dashboard`, `Sales`, `Purchases`, `Products`, `Categories`, `Units`, `Customers`, `Suppliers`, `FinanceCategories`, `FinanceTransactions`, `Users`, `Settings`, `Profile`

### Service Layer
Business logic is separated into services in `app/Services/`:
`CategoryService`, `CustomerService`, `DashboardStatsService`, `FinanceCategoryService`, `FinanceTransactionService`, `ProductService`, `ProfileService`, `PurchaseService`, `SaleService`, `SupplierService`, `UnitService`, `UserService`

### DTOs
Data Transfer Objects in `app/DTOs/` are used to pass structured data between layers.

### Form Requests
All input validation uses Laravel Form Requests in `app/Http/Requests/`.

### Currency Formatting
A global Blade directive handles currency display:
```php
@money($amount)  // Formats amounts using locale settings from DB
```
Implementation: `app/Helpers/CurrencyHelper.php`

### AJAX API Endpoints
Lightweight API controllers in `app/Http/Controllers/Api/` serve AJAX requests from Livewire components (product search, supplier/customer lookups, etc.).

---

## Database Models

| Model | Description |
|-------|-------------|
| User | Authentication & authorization |
| Customer | Customer records |
| Supplier | Supplier records |
| Product | Inventory items |
| Category | Product categories |
| Unit | Units of measurement |
| Purchase | Purchase orders |
| PurchaseItem | Line items for purchases |
| Sale | Sales transactions |
| SaleItem | Line items for sales |
| FinanceCategory | Finance ledger categories |
| FinanceTransaction | Finance ledger entries |
| Setting | App-wide configuration (currency, locale) |

---

## Frontend Stack

| Tool | Purpose |
|------|---------|
| Tailwind CSS v4 | Utility-first CSS framework |
| Alpine.js v3 | Lightweight JS reactivity |
| Vite | Asset bundling and dev server |
| Blade Heroicons | SVG icon library |
| Livewire PowerGrid | Data table component |
| Flatpickr | Date picker |
| Tom Select | Searchable select inputs |
| ApexCharts | Dashboard analytics charts |
| Axios | HTTP client for AJAX |

---

## Code Quality

```bash
# Format code with Laravel Pint (PSR-12 + Laravel conventions)
php artisan pint

# Or check without fixing
php artisan pint --test
```

### Commit Conventions
Follow conventional commits:
```
feat: add new feature
fix: resolve bug
refactor: restructure code without behavior change
docs: documentation updates
test: add or update tests
```

---

## Development Workflow

### Adding a New Module
1. Create Eloquent model: `php artisan make:model MyModel -m`
2. Create service: `app/Services/MyModelService.php`
3. Create Livewire component: `php artisan make:livewire MyModule/Index`
4. Add Blade view: `resources/views/livewire/my-module/`
5. Register route in `routes/web.php`

### Adding a New Route
Routes are in `routes/web.php`. Auth-protected routes are wrapped in:
```php
Route::middleware('auth')->group(function () {
    // your routes here
});
```

### Modifying Settings/Currency
Settings (currency symbol, locale, decimal places) are stored in the `settings` table and managed via the Settings Livewire component. The `CurrencyHelper` reads from this table to format all `@money()` output.

---

## Useful Tools

```bash
# Interactive REPL (run PHP code against live app)
php artisan tinker

# Tail logs in real time
php artisan pail --timeout=0

# Debug toolbar is available in dev (barryvdh/laravel-debugbar)
# Access at: http://localhost:8000?debugbar=true

# SQLite database file location
database/database.sqlite

# Uploaded files (after storage:link)
storage/app/public/  →  public/storage/
```

---

## Troubleshooting

**Assets not loading?**
```bash
npm run build  # or npm run dev for watch mode
```

**Database errors after pulling changes?**
```bash
php artisan migrate
```

**Full reset (wipe and re-seed everything)?**
```bash
php artisan migrate:fresh --seed
```

**Permission errors on storage?**
```bash
chmod -R 775 storage bootstrap/cache
```

**Queue jobs not processing?**
```bash
php artisan queue:listen --tries=1
```
