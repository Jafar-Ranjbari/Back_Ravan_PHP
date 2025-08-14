## php artisan migrate

## php artisan db:seed

## php artisan l5-swagger:generate

## php artisan tinker


\App\Models\Institute::create([
    'name' => 'Test Institute',
    'address' => '123 Main St',
    'logo_url' => null,
    'start_date' => now(),
    'end_date' => now()->addYear(),
    'mobile' => '1234567890',
    'username' => 'institute_admin',
    'password' => bcrypt('password')
]);


## request body is 

{
  "full_name": "John Doe",
  "email": "john@example.com",
  "mobile": "1234567890",
  "sex": "male",
  "age": 28,
  "password": "secret123",
  "role_id": 4,
  "institute_id": 1
}

## nohup php artisan serve --host=162.240.109.253 --port=8090 > /dev/null 2>&1 &
