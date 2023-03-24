# Mini-Aspire API

This API allows authenticated users to go through a loan application, submit the weekly loan repayments, and view their own loans.

## Table of Contents
1. [Routes](#routes)
2. [Loan Routes](#loan-routes)
3. [Repayment Routes](#repayment-routes)
4. [Authentication Routes](#authentication-routes)
5. [Middleware](#middleware)
6. [Makefile](#makefile)
7. [API Documentation](#api-documentation)

## Routes
All routes except for the authentication routes require users to be authenticated and logged in.

## Loan Routes
- POST /loans: Allows customers to create a loan request by defining the loan amount and term. Upon submission, the system will generate three scheduled repayments based on a weekly repayment frequency. The loan and scheduled repayments will have a status of "PENDING".
- GET /loans: Allows admins to view a list of all loan requests. Only admins can access this route.
- GET /loans/{id}: Allows admins to view the details of a specific loan request. Only admins can access this route.
- PUT /loans/{id}: Allows admins to update the details of a specific loan request. Only admins can access this route.
- DELETE /loans/{id}: Allows admins to delete a specific loan request. Only admins can access this route.
- PUT /loans/{id}/approve: Allows admins to approve a specific loan request, changing its status from "PENDING" to "APPROVED". Only admins can access this route.

## Repayment Routes
- POST /repayments: Allows customers to add a repayment with an amount greater than or equal to the scheduled repayment. Upon submission, the scheduled repayment status will change to "PAID". If all scheduled repayments connected to a loan are "PAID", the loan status will also change to "PAID".

## Authentication Routes
- POST /register: Allows users to register a new account by providing their name, email, and password.
- POST /login: Allows users to log in by providing their email and password.
- POST /logout: Allows users to log out.

## Middleware
The following middleware are used to protect the routes:
- check_logged_in: Checks if the user is authenticated and logged in.
- only_admin: Checks if the user is an admin.
- check_user_belongs_to_repayment: Checks if the user is the owner of the repayment.

## Makefile

This Makefile contains several targets to make it easier to manage and test a PHP Laravel application. 

### Targets:

- `run`: Runs the Laravel application using the built-in PHP development server via `php artisan serve`.
- `clear.config`: Clears the configuration cache.
- `docker.start`: Starts the Docker environment using Docker Compose via `docker-compose up -d`.
- `docker.stop`: Stops the Docker environment via `docker-compose down`.
- `migrate.seed`: Seeds the database via `php artisan db:seed`.
- `migrate.db`: Runs database migrations via `php artisan migrate`.
- `migrate.reset`: Rolls back all database migrations via `php artisan migrate:reset`.
- `test.migrate.db`: Runs database migrations for the testing environment via `php artisan migrate --env=testing`.
- `test`: Runs tests via `php artisan test --env=testing`.
- `test.migrate.reset`: Rolls back all database migrations for the testing environment via `php artisan migrate:reset --env=testing`.

## API Documentation

### GET `/loans`

```
Payload: {}
```

```
Status: 200 OK
  {
      "status": "success",
      "data": [
          {
              "id": 1,
              "user_id": 1,
              "amount": 1000,
              "term": 12,
              "state": "APPROVED",
              "created_at": "2022-02-25T05:56:29.000000Z",
              "updated_at": "2022-02-25T05:56:29.000000Z"
          },
          {
              "id": 2,
              "user_id": 1,
              "amount": 5000,
              "term": 24,
              "state": "PENDING",
              "created_at": "2022-02-25T06:07:17.000000Z",
              "updated_at": "2022-02-25T06:07:17.000000Z"
          }
      ]
  }

```

Returns a list of all loans. Each loan object has the following properties:

- id: unique identifier for the loan (integer)
- user_id: the ID of the user who requested the loan (integer)
- amount: the amount of the loan (decimal, 10 digits including 2 decimal places)
- term: the term of the loan in months (integer)
- state: the state of the loan, can be "PENDING", "APPROVED" (string)
- created_at: the date and time the loan was created (string)
- updated_at: the date and time the loan was last updated (string)

### GET `/loans/{id}`

```
Payload: {}
```

```
Status: 200 OK - Returns an instance of loan based on the given ID.
  {
    "status": "success",
    "data": {
        "id": 2,
        "user_id": 1,
        "amount": "10.00",
        "term": 2,
        "state": "APPROVED",
        "created_at": "2023-03-23T12:15:52.000000Z",
        "updated_at": "2023-03-23T12:21:53.000000Z",
        "scheduled_repayments": [
            {
                "id": 4,
                "loan_id": 2,
                "due_date": "2023-04-06",
                "amount": "5.00",
                "state": "PENDING",
                "created_at": "2023-03-23T12:15:53.000000lZ",
                "updated_at": "2023-03-23T12:15:53.000000Z"
            },
            {
                "id": 3,
                "loan_id": 2,
                "due_date": "2023-03-30",
                "amount": "5.00",
                "state": "PAID",
                "created_at": "2023-03-23T12:15:53.000000Z",
                "updated_at": "2023-03-23T12:22:50.000000Z"
            }
        ]
    }
  }

```


```
Status: 404 Not Found - Returns 404 when the given ID is non existance. 

  {
     "status": "error",
      "message": "Loan not found"
  }

```


### POST `/loans`

```
Payload: { "amount": "", "term": "2" }
```

> amount (required): The amount of the loan
>
> term (required): The term of the loan in weeks

```
Status: 201 Created - Returns an instance of the created loan with the scheduled payment breakdown
  {
    "status": "success",
    "data": {
        "user_id": 1,
        "amount": "2000",
        "term": 3,
        "state": "PENDING",
        "updated_at": "2023-03-24T06:47:01.000000Z",
        "created_at": "2023-03-24T06:47:01.000000Z",
        "id": 9,
        "scheduled_repayments": [
            {
                "id": 25,
                "loan_id": 9,
                "due_date": "2023-03-31",
                "amount": "666.67",
                "state": "PENDING",
                "created_at": "2023-03-24T06:47:01.000000Z",
                "updated_at": "2023-03-24T06:47:01.000000Z"
            },
            {
                "id": 26,
                "loan_id": 9,
                "due_date": "2023-04-07",
                "amount": "666.67",
                "state": "PENDING",
                "created_at": "2023-03-24T06:47:01.000000Z",
                "updated_at": "2023-03-24T06:47:01.000000Z"
            },
            {
                "id": 27,
                "loan_id": 9,
                "due_date": "2023-04-14",
                "amount": "666.67",
                "state": "PENDING",
                "created_at": "2023-03-24T06:47:01.000000Z",
                "updated_at": "2023-03-24T06:47:01.000000Z"
            }
        ]
    }
  }
```
```
Status: 422 Unprocessable Content

  {
      "status": "error",
      "message": {
          "amount": [
              "The amount field is required."
          ]
      }
  }
```

### PUT `/loans/{id}/approve`

```
Payload: {}
```

```
Status: 200 OK - Returns an updated loan with state APPROVED
  {
      "status": "success",
      "data": {
          "id": 11,
          "user_id": 1,
          "amount": "5000.00",
          "term": 2,
          "state": "APPROVED",
          "created_at": "2023-03-24T12:47:26.000000Z",
          "updated_at": "2023-03-24T12:47:57.000000Z"
      }
  }
```

```
Status: 404 Not Found - Returns 404 when the given ID is non existance. 

  {
     "status": "error",
      "message": "Loan not found"
  }
```

### POST `/repayments/{id}`

```
{"scheduled_repayment_id": "30", "amount":2000}
```

```
Status: 200 OK - Success create a repayment for scheduled_repayment. If the repayment amount is less than the total scheduled_repayment, it'll update the status to 'PARTIAL', else 'PAID'
  {
      "status": "success",
      "data": {
          "scheduled_repayment_id": "30",
          "amount": 2000,
          "updated_at": "2023-03-24T12:51:29.000000Z",
          "created_at": "2023-03-24T12:51:29.000000Z",
          "id": 5
      }
  }
```
```
Status: 422 - When total repayment amount greater than the scheduled repayment amount
  {
      "status": "error",
      "message": "The repayment amount cannot be greater than the scheduled repayment amount."
  }
```
```
Status: 422 - When the scheduled repayment has been fully paid
  {
      "status": "error",
      "message": "The scheduled repayment has been fully paid."
  }
```
```
Status: 422 - When the loan of the scheduled_repayment is not approved
  {
      "status": "error",
      "message": "Can not make repayment. Your loan has not been approved."
  }
```