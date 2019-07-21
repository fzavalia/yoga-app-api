# Getting Started

## Local Development

Copy Environment Variable

`$ cp example.env .env`

Run Database

`$ docker-compose up -d`

Prepare Database

`$ scripts/migrate_and_seed_local_db.sh`

Run Server

`$ php artisan serve`

The server will be exposed at `http://localhost:8000`

Local credentials:

- username: `test@gmail.com` || `test2@gmail.com`
- password: `password`