echo "Fresh Migration"

php artisan migrate:fresh > /dev/null

echo "Passport"

php artisan passport:install  > /dev/null

echo "Seeding User Table"

php artisan db:seed --class UserTableSeeder  > /dev/null

echo "Seeding Student Table"

php artisan db:seed --class StudentTableSeeder  > /dev/null

echo "Done!"
