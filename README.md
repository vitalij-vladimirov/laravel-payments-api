---
First launch
---
1. `docker-compose up` - App will launch and install all dependencies, it may take from 5 to 15 min. depending on you PC speed.
2. `docker exec -it kernolab bash` - login to docker container
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
    "user_id": 2,
    "details": "Transaction number two",
    "receiver_account": "LT165134865135165135106",
    "receiver_name": "Someone Somewhere",
    "amount": 900,
    "currency": "eur"
 }
```

```
Response:
{
    "transaction_id": 1,
    "details": "Transaction number two",
    "receiver_account": "LT165134865135165135106",
    "receiver_name": "Someone Somewhere",
    "amount": 900,
    "fee": 90,
    "currency": "eur",
    "status": "received",
    "error_code": null,
    "error_message": null
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
    "details": "Transaction number two",
    "receiver_account": "LT165134865135165135106",
    "receiver_name": "Someone Somewhere",
    "amount": 900,
    "fee": 90,
    "currency": "eur",
    "status": "approved",
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
    "transaction_id": 100,
    "details": "Transaction number two",
    "receiver_account": "LT165134865135165135106",
    "receiver_name": "Someone Somewhere",
    "amount": 900,
    "fee": 90,
    "currency": "eur",
    "status": "approved",
    "error_code": null,
    "error_message": null
}
```

Postman Examples included to project as `Kernolab.postman_collection.json`.