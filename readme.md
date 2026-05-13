# CLT Toolbox Feature Test

Laravel 11 implementation for the CLT Toolbox feature test.

The application manages this hierarchy:

```text
Supplier -> CLT Layups -> CLT Layers
```

It includes CRUD flows, supplier JSON import/export, conflict detection, and manual conflict resolution.

## Implemented Features

- CRUD Suppliers
- CRUD CLT Layups nested under Supplier
- CRUD CLT Layers nested under Layup
- Export Supplier data as JSON, including all related layups and layers
- Import Supplier JSON data into an existing supplier
- Conflict detection when an incoming layer has the same `layer_order` but different `thickness`, `width`, or `angle`
- Manual conflict resolution page with Existing Version and Incoming Version side by side
- Service pattern with interface binding via `AppServiceProvider`
- Form Request validation
- Policies/Gates for authorization
- Route Model Binding
- Unit and Feature tests

## Requirements

Make sure these are installed:

- PHP `^8.2`
- Composer
- Node.js and npm
- SQLite PHP extension enabled

This project uses SQLite by default, so no MySQL/PostgreSQL setup is required for local review.

## Installation

Clone the repository and checkout the assignment branch:

```bash
git clone https://github.com/dzackygo/candidate-test.git
cd candidate-test
git checkout dzacky-assignment
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Create the SQLite database file:

```bash
touch database/database.sqlite
```

On Windows PowerShell, use:

```powershell
New-Item database/database.sqlite -ItemType File -Force
```

Make sure the database connection in `.env` is:

```env
DB_CONNECTION=sqlite
```

Then run migrations and seed the demo user:

```bash
php artisan migrate --seed
```

Build frontend assets:

```bash
npm run build
```

Run the application:

```bash
php artisan serve
```

Open the app:

```text
http://127.0.0.1:8000
```

## Demo Login

Use this account after running the seeder:

```text
Email: test@example.com
Password: password
```

The password is provided by `database/factories/UserFactory.php`.

## Suggested Review Flow

1. Login using the demo account.
2. Open `Suppliers`.
3. Create a supplier.
4. Open the supplier detail page.
5. Create a CLT layup under that supplier.
6. Create CLT layers under that layup.
7. Edit and delete sample supplier, layup, and layer records.
8. Export the supplier JSON.
9. Import a JSON file into the supplier.
10. Import conflicting data and resolve it from the conflict resolution page.

## JSON Import Example

Use this structure for a normal import:

```json
{
  "supplier": {
    "name": "Demo Supplier"
  },
  "layups": [
    {
      "name": "Wall Layup A",
      "layers": [
        {
          "layer_order": 1,
          "thickness": 20,
          "width": 1200,
          "angle": 0
        },
        {
          "layer_order": 2,
          "thickness": 35,
          "width": 1200,
          "angle": 90
        }
      ]
    }
  ]
}
```

To trigger a conflict, import data with the same layup `name` and the same `layer_order`, but change one or more of:

```text
thickness
width
angle
```

Example conflict payload:

```json
{
  "supplier": {
    "name": "Demo Supplier"
  },
  "layups": [
    {
      "name": "Wall Layup A",
      "layers": [
        {
          "layer_order": 1,
          "thickness": 30,
          "width": 1300,
          "angle": 90
        }
      ]
    }
  ]
}
```

## Running Tests

Run all tests:

```bash
php artisan test
```

Run Laravel Pint:

```bash
vendor/bin/pint --test
```

On Windows PowerShell:

```powershell
vendor\bin\pint --test
```

Build assets:

```bash
npm run build
```

## Main Files

- Models: `app/Models/Supplier.php`, `app/Models/CltLayup.php`, `app/Models/CltLayer.php`
- Controllers: `app/Http/Controllers/SupplierController.php`, `CltLayupController.php`, `CltLayerController.php`
- Import/export controllers: `SupplierImportExportController.php`, `SupplierImportConflictController.php`
- Services: `app/Services/SupplierExportService.php`, `SupplierImportService.php`, `SupplierImportConflictService.php`
- Form Requests: `app/Http/Requests`
- Policies: `app/Policies`
- Feature tests: `tests/Feature`
- Unit tests: `tests/Unit`

## ERD

![ERD](./erd-new.png)
