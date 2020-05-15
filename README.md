---
First launch
---
1. `docker-compose up` - App will launch and install all dependencies, it may take from 5 to 15 min. depending on you PC speed.
2. `docker exec -it payment-api bash` - login to docker container
3. `php artisan migrate` - create database tables
4. `php artisan db:seed` - create default database values
5. `exit`

---
Next launches
---
For next launches type `docker-compose up -d` to run App in background. Full launch should take about 1-2 min.

---
Short API documentation
---
#### Set transaction
URL: `http://localhost:801/api/transaction`

Method: `POST`

```
Request:
{
    "user_id": 1,
    "details": "Test transaction",
    "receiver_account": "LT000000000000000001",
    "receiver_name": "John Doe",
    "amount": 100,
    "currency": "eur"
 }
```

```
Response:
{
    "transaction_id": 1,
    "details": "Test transaction",
    "receiver_account": "LT000000000000000001",
    "receiver_name": "John Doe",
    "amount": 100,
    "fee": 10,
    "currency": "eur",
    "status": "received",
    "error_code": null,
    "error_message": null
}
```

```
Critical error Response:
{
    "error_code": "bad_input",
    "error_message": "Bad input data"
}
```
---
#### Submit (approve) transaction
URL: `http://localhost:801/api/transaction/{transaction_id}/confirm`

Method: `POST`

```
Request:
{
    "code": 111
}
```

```
Response:
{
    "transaction_id": 1,
    "details": "Test transaction",
    "receiver_account": "LT000000000000000001",
    "receiver_name": "John Doe",
    "amount": 100,
    "fee": 10,
    "currency": "eur",
    "status": "confirmed",
    "error_code": null,
    "error_message": null
}
```
---
#### Get transaction
URL: `http://localhost:801/api/transaction/{transaction_id}`

Method: `GET`
```
Response:
{
    "transaction_id": 1,
    "details": "Test transaction",
    "receiver_account": "LT000000000000000001",
    "receiver_name": "John Doe",
    "amount": 100,
    "fee": 10,
    "currency": "eur",
    "status": "completed",
    "error_code": null,
    "error_message": null
}
```

Postman Examples included to project as `payment_api_task.postman_collection.json`.

---
PhpUnit testing
---
1. `docker exec -it payment-api bash` - login to docker container
2. `vendor/bin/phpunit` - run Features tests