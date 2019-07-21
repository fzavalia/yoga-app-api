# Getting Started

## Requires

- php

- composer

## Local Development

Install dependencies

`$ composer install`

Copy environment variables

`$ cp example.env .env`

Run database

`$ docker-compose up -d`

Prepare database

`$ scripts/migrate_and_seed_local_db.sh`

Run server

`$ php artisan serve`

The server will be exposed at `http://localhost:8000`

Local credentials:

- username: `test@gmail.com` || `test2@gmail.com`
- password: `password`