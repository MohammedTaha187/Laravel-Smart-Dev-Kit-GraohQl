# 🚀 Laravel Smart Dev Kit (Easy Dev)

A powerful, production-ready SDK for rapid Laravel API development. This kit follows the **Interface-Service-Repository** architecture and automates the creation of 11+ essential files per CRUD module.

---

## ✨ Features

- **Standard & Industrial Modes**: Choose between a monolithic or modular architecture.
- **Automated CRUD Generation**: Models, Migrations, Controllers, Services, Repositories, DTOs, and more.
- **Smart Data Syncing**: Instantly update your code after database schema changes.
- **Docker Ready**: Fully compatible with Laravel Sail.
- **Clean Architecture**: Strict separation of concerns for maintainable enterprise code.

---

## 🛠️ Environment Support

### 💻 Local Development
If you are running PHP/Laravel directly on your machine:
```bash
php artisan smart:crud ModelName
```

### 🐳 Docker (Laravel Sail)
If you are using the official Docker setup:
```bash
./vendor/bin/sail artisan smart:crud ModelName
```

---

## 🏗️ Choice of Architecture

### 1. Standard Mode (Monolithic)
Ideal for small-to-medium projects. Files are placed in the standard `app/` directory with our premium nested organization.
```bash
# Example
php artisan smart:crud Product
```

### 2. Industrial Mode (Modular)
Designed for large-scale enterprise apps (like big tech companies). Encapsulates each feature into a self-contained module.
```bash
# Example
php artisan smart:crud Product --module=Catalog
```

---

## 🛠️ Step-by-Step Implementation Guide

Follow this guide to build a professional **Product** module with automated image and video handling.

### 1️⃣ Phase 1: Database Design
Create your migration: `php artisan make:migration create_products_table`.  
Use **Media-Aware** naming conventions (`*_image`, `*_video`, `*_file`, `*_url`) for automatic handling:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->string('thumbnail_image'); // Treated as Image (Upload)
    $table->string('demo_video');      // Treated as Video (Upload)
    $table->string('catalog_pdf');    // Treated as Document (Upload)
    $table->string('official_url');    // Treated as external URL
    $table->timestamps();
});
```

### 2️⃣ Phase 2: Generation
Run the smart command to generate all 11 enterprise layers:
```bash
php artisan smart:crud Product --module=Catalog
```

### 3️⃣ Phase 3: Deployment
Run the migration and link the storage to make your media public:
```bash
php artisan migrate
php artisan storage:link
```

### 4️⃣ Phase 4: Verification
Your API is now ready! 
- **Upload**: Send a `POST` request with `multipart/form-data`. The `ProductService` will automatically store the files.
- **Response**: The `ProductResource` will automatically return full public URLs:
```json
{
    "id": 1,
    "name": "Super Widget",
    "thumbnail_image": "https://yourdomain.com/storage/products/xyz.jpg",
    "demo_video": "https://yourdomain.com/storage/products/abc.mp4",
    "official_url": "https://google.com"
}
```

---

## 🔄 Syncing Database Changes

When you update your database (e.g., adding a new column to a migration), follow these steps to **Sync** your code automatically:

1.  **Update Migration**: Add your new columns to the migration file.
2.  **Run Migrate**: `php artisan migrate`
3.  **Run Sync Command**:
    ```bash
    php artisan smart:from-migration table_name --force
    ```
    > [!TIP]
    > Using the `--force` flag will automatically update your **StoreRequest**, **UpdateRequest**, and **Resource** with the new database columns without asking for confirmation.

---

## 📖 Installation & Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

---

## 🧪 Testing

```bash
./vendor/bin/sail artisan test
```

---

## 🤝 Contribution & License
Developed by **Muhammad Taha**. Licensed under the **MIT License**.
