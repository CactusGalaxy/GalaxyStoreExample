# Galaxy store

Just simple Laravel app with some examples.

- Filament admin panel
- Astrotomic Translations 

and possibly more in the future 😉

## Requirements

This project requires PHP 8.1 and uses [Laravel 10](https://laravel.com/docs/10.x/releases)

## Installation

Clone the project

```bash
git clone https://github.com/CactusGalaxy/GalaxyStoreExample.git
```

Go to the project directory

```bash
cd GalaxyStoreExample
```

Copy env.example and set it up (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

```bash
cp .env.example .env
```

Install composer dependencies

```bash
composer install
```

Install npm packages and make build

```bash
npm install && npm run build
```

Generate app key

```bash
php artisan key:generate
```

Generate storage symlink

```bash
php artisan storage:link
```

Run migrations and seeders with sample data

```bash
php artisan migrate --seed
```

## Development

Use local app server

```bash
php artisan serve
```

and Vite dev server for frontend assets

```bash
npm run dev
```
