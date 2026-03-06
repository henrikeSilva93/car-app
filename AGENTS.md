# AGENTS.md - Car App Developer Guide

## Project Overview
- **Project**: Car App - Vehicle management system
- **Stack**: Laravel 12, Livewire/Volt, Flux UI, Tailwind CSS, SQLite
- **PHP Version**: ^8.2
- **Repository**: Laravel skeleton application

---

## Commands

### Setup & Installation
```bash
# Full setup (install deps, generate key, migrate, build assets)
composer run setup

# Install PHP dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Install and build frontend
npm install && npm run build
```

### Development Server
```bash
# Run full dev environment (server, queue, logs, vite)
composer run dev

# Run Laravel server only
php artisan serve
```

### Testing
```bash
# Run all tests
php artisan test
# or
composer test

# Run specific test file
php artisan test tests/Feature/CarTest.php

# Run specific test method
php artisan test --filter=test_can_create_car

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Format specific file
./vendor/bin/pint app/Models/Car.php

# Run Pint with dry-run (check only)
./vendor/bin/pint --test
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset and re-run migrations
php artisan migrate:fresh

# Reset, re-run migrations and seed
php artisan migrate:fresh --seed
```

### Artisan Commands
```bash
# Clear config cache
php artisan config:clear

# Clear all caches
php artisan optimize:clear

# List routes
php artisan route:list
```

---

## Code Style Guidelines

### PHP Standards
- **PSR-12** coding style (enforced by Laravel Pint)
- Use strict types: `declare(strict_types=1);` at top of PHP files
- Always use PHP 8+ features: typed properties, named arguments, null-safe operator

### Naming Conventions
- **Classes**: PascalCase (`Car`, `PrismService`, `StatisticService`)
- **Methods**: camelCase (`getCars()`, `createCar()`, `updateCar()`)
- **Variables**: camelCase (`$userId`, `$carId`, `$selectedCar`)
- **Relationships**: camelCase without parentheses when used as properties (`$car->maintenances`)
- **Database_case plural (`cars tables**: snake`, `fuellings`, `maintenances`)
- **Database columns**: snake_case (`user_id`, `created_at`, `car_id`)

### Imports & Namespaces
```php
// Always use strict types
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Import classes at top of file
use App\Models\Car;
use App\Models\Maintenance;
use App\Services\PrismService;
```

### Type Hints & Return Types
```php
// Always add return types
public function getCars(): array
{
    return Car::where('user_id', auth()->id())->get()->toArray();
}

// Use typed properties
public string $name;
public ?int $carId = null;
public array $cars = [];

// Nullable types
public function findCar(?int $id): ?Car
{
    return $id ? Car::find($id) : null;
}
```

### Models & Eloquent
```php
// Define fillable, hidden, casts
class Car extends Model
{
    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'plate',
        'mileage',
    ];

    // Always define relationships
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Security & Authorization
```php
// ALWAYS verify ownership before edit/delete operations
public function editCar($carId)
{
    $car = Car::where('id', $carId)
        ->where('user_id', auth()->id())
        ->first();

    if (!$car) {
        return; // or show error
    }
    // proceed with edit
}

// Use whereHas for checking ownership through relationships
$maintenance = Maintenance::where('id', $id)
    ->whereHas('car', fn($q) => $q->where('user_id', auth()->id()))
    ->first();
```

### Error Handling
```php
// Always handle exceptions in services
try {
    $result = SomeService::doSomething();
} catch (\Exception $e) {
    \Log::error('Error: ' . $e->getMessage());
    return response()->json(['error' => 'Something went wrong'], 500);
}

// Use try-catch in chatbot tools
try {
    $maintenance = Maintenance::create([...]);
    return "Success";
} catch (\Exception $e) {
    return "Error: " . $e->getMessage();
}
```

### Livewire Components
```php
// Use classes extending Component, not anonymous classes in views
// Location: app/Livewire/CarManager.php

new class extends Component
{
    public array $cars = [];
    public array $car = ['brand' => '', 'model' => '', ...];

    public function mount(): void
    {
        $this->cars = $this->loadCars();
    }

    public function loadCars(): array
    {
        return Car::where('user_id', auth()->id())->get()->toArray();
    }
};
```

### Blade Templates
- Use Tailwind CSS classes for styling
- Use Flux UI components (`<flux:button>`, `<flux:modal>`, `<flux:input>`)
- Always use proper indentation
- Use Livewire directives (`wire:model`, `wire:click`)

### Git & Commits
- Use meaningful commit messages
- Follow conventional commits: `type: description`
  - `feat: add new feature`
  - `fix: fix bug`
  - `security: security fix`
  - `refactor: refactor code`

---

## Project Structure

```
car-app/
├── app/
│   ├── Models/          # Eloquent models
│   ├── Services/        # Business logic (PrismService, StatisticService)
│   ├── Livewire/        # Livewire components
│   └── Http/Controllers/# HTTP controllers
├── database/
│   ├── migrations/      # Database migrations
│   ├── factories/       # Test factories
│   └── seeders/        # Database seeders
├── resources/
│   └── views/          # Blade templates
│       └── pages/      # Page components
├── tests/
│   └── Feature/        # Feature tests
├── routes/             # Web routes
└── bootstrap/          # Laravel bootstrap
```

---

## Important Notes

### Security First
- Never hardcode user IDs (always use `auth()->id()`)
- Always verify resource ownership before operations
- Never commit secrets to `.env` (already in `.gitignore`)
- Use parameterized queries (Laravel ORM handles this)

### Testing
- Write feature tests for all CRUD operations
- Use factories for test data: `Car::factory()->create()`
- Use `RefreshDatabase` trait for tests
- Test ownership verification: ensure users can only access their own data

### Database
- Use SQLite for development
- Always use migrations for schema changes
- Define foreign keys with `constrained()`
- Use `onDelete('cascade')` for related records
