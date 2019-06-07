echo "Fresh Migration"

php artisan migrate:fresh

echo "Passport"

php artisan passport:install

echo "Seeding User Table"

php artisan db:seed --class UserTableSeeder

echo "Seeding Student Table"

php artisan db:seed --class StudentTableSeeder

echo "Done!"
