# Laravel Smart CRUD Starter Kit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/muhammad/starter-kit.svg?style=flat-square)](https://packagist.org/packages/muhammad/starter-kit)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/muhammad/starter-kit/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/muhammad/starter-kit/actions)

A Laravel package designed to simplify API development by automating monotonous tasks while following Clean Architecture principles.

## Features

- **CRUD Generator**: Generates Models, Services, Repositories, and Tests.
- **Relationship Discovery**: Detects foreign keys and syncs them as Eloquent relationships.
- **Validation Auto-generation**: Creates validation rules based on database metadata.

## Quick Start

1.  **Migrations**: Define your database schema using standard migrations.
2.  **Migrate & Sync**: Run `php artisan migrate` followed by `php artisan smart:sync-relations`.
3.  **Generate Feature**: Run `php artisan smart:crud Product --api --with-service --with-tests`.
4.  **Implement Logic**: The boilerplate is generated; you can now focus on the business requirements.

## 📦 Installation

```bash
composer require muhammad/starter-kit --dev
```

Publish stubs (optional):
```bash
php artisan vendor:publish --tag="starter-kit-stubs"
```

## 🧪 Testing

```bash
composer test
```

## 📜 License

The MIT License (MIT).
