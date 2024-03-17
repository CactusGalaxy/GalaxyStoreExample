# Galaxy store

Just simple Laravel playground with examples.

Focused on testing package - [Filament Astrotomic plugin](https://github.com/CactusGalaxy/FilamentAstrotomic)

Also, in this project installed:

- [Filament admin panel](https://filamentphp.com/)
  Admin panel

- Laravel Translations by [Astrotomic](https://docs.astrotomic.info/laravel-translatable)
  localization for Models

- Laravel Localization by [mcamara package](https://github.com/mcamara/laravel-localization)
  easy localize routes for application

- [Spatie data](https://spatie.be/docs/laravel-data/v4/introduction)
  for storing json typed values for Home page

- [Spatie Settings](https://github.com/spatie/laravel-settings) with [filament plugin](https://filamentphp.com/plugins/filament-spatie-settings)
  store settings (like site name, email etc.)

- [Spatie Login link](spatie/laravel-login-link)
  to quickly login to the admin panel

and possibly more in the future 😉

![product-admin.png](/resources/art/product-admin.png)

## Requirements

This project requires PHP 8.1 and uses [Laravel 10](https://laravel.com/docs/10.x/releases)

## Local Development

If you want to work on this project on your local machine, you may follow the instructions below.
These instructions assume you are store the sites in your `~/Sites` directory:

1. Fork this repository
2. Open your terminal and `cd` to your `~/Sites` folder
3. Clone your fork into the `~/Sites/GalaxyStore` folder, by running the following command *with your username placed into the {username} slot*:
    ```bash
    git clone git@github.com:{username}/GalaxyStoreExample GalaxyStore
    ```
4. CD into the new directory you just created:
    ```bash
    cd GalaxyStoreExample
    ```
5. Run the `setup.sh` bin script, which will take all the steps necessary to prepare your local installation:
    ```bash
    ./bin/setup.sh
    ```
6. Set up database access (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

7. Run migrations and seeders with sample data

    ```bash
    php artisan migrate --seed
    ```

## Admin panel access

```yaml
email: admin@admin.com
password: admin
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

## Edit package



```
Sites
|-packages/CactusGalaxy.FilamentAstrotomic
|-GalaxyStore
```

```json
"repositories": [
    {
        "type": "path",
        "url": "../packages/CactusGalaxy.FilamentAstrotomic",
        "options": {
            "symlink": true
        }
    }
],
```
