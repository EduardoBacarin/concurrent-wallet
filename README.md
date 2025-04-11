## Concurrent Wallet Documentation

This project is for portfolio only. Consists in a Money Wallet where you can debit or credit some value, the idea in this project is simple, but when we put some concurrency it became a little different, "it's not only a CRUD".

In this project I use some Laravel features, like Queues, Jobs, Observers, custom Exceptions and Form Requests.<br>
It's possible to use "exists" and "unique" in Form Requests to validate email, but I prefer to use in code validation to use the correct HTTP Code in response.

The "amount" field is INTEGER, so "100" is equal to "1.00".

You can find a Postman Collection in project root followed by name "postman-collection.json", you can import this file in your Postman and test the endpoints.

You can see other projects with in my GitHub profile: <a href="https://github.com/EduardoBacarin">https://github.com/EduardoBacarin</a>

**What is concurrency?**

Concurrency means multiple computations are happening at the same time. - <a href="https://web.mit.edu/6.005/www/fa14/classes/17-concurrency/">MIT</a><br>
What's the problem? In a wallet service, imagine you have only "US$10.00" in your wallet. If you try to withdraw "US$10.00" twice at exactly the same time, without proper concurrency handling, you might end up withdrawing "US$20.00", even though there is only "US$10.00" in the wallet. This results in money being multiplied. So, in a financial service, you MUST handle concurrency.

Here's a paper from the Department of Computer Science at Columbia University that explains what a <a href="https://www.usenix.org/system/files/conference/hotpar12/hotpar12-final44.pdf">Concurrency Attack</a> is and the associated problems.

### Disclaimer about Docker

I don't like to use Docker containers because is a waste of computer resource. I made this project using Linux Ubuntu as a Windows Subsystem with all services running in this subsystem, I think it's better, easier and lightweight.
It was tested on Linux Mint too.

### Requirements

-   PHP 8.3
-   PostgreSQL
-   Redis
-   RoadRunner

### Setup

1. Download or clone repository
2. Copy .env.example and rename to .env
3. Copy .rr.yaml.example and rename to .rr.yaml
4. Configure .env file as your system
5. Generate Application key

```
    php artisan key:generate
```

5. Install all dependencies

```
    composer install
```

3. Run Unit and Feature tests

```
    php artisan test
```

4. Run server

```
    php artisan octane:start
```

5. Check if connections are Ok hitting "https://127.0.0.1:8000"

### Auth

#### Register

[POST] /api/auth/register

This endpoint makes the user registration.

Headers:

| Header       | Value            |
| ------------ | ---------------- |
| Accept       | application/json |
| Content-type | application/json |

Body: JSON

| Property | Description   | Required | Condition | Type   |
| -------- | ------------- | -------- | --------- | ------ |
| name     | User name     | yes      | ----      | String |
| email    | User email    | yes      | ----      | String |
| password | User password | yes      | ----      | String |

Responses:

**_ HTTP Code 201 - Created _**

```
    {
        "success": true,
        "code": 201,
        "message": "User has been created"
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "code": 400,
        "message": "User creation failed"
    }
```

**_ HTTP Code 409 - Conflict - User already exists _**

```
    {
        "success": false,
        "code": 409,
        "message": "The email has already been taken."
    }
```

**_ HTTP Code 422 - Unprocessable Entity - Validation error _**

```
    {
        "success": false,
        "code": 422,
        "message": "The email field must be a valid email address."
    }
```

#### Login

[POST] /api/auth/login

This endpoint makes user login and generante a bearer token

Headers:

| Header       | Value            |
| ------------ | ---------------- |
| Accept       | application/json |
| Content-type | application/json |

Body:

| Property | Description   | Required | Condition | Type   |
| -------- | ------------- | -------- | --------- | ------ |
| email    | User email    | yes      | ----      | String |
| password | User password | yes      | ----      | String |

Responses:

**_ HTTP Code 201 - Created _**

```
    {
        "success": true,
        "code": 200,
        "message": "Login was successful",
        "data": {
            "token": "...."
        }
    }
```

**_ HTTP Code 404 - Not Found _**

```
    {
        "success": false,
        "code": 404,
        "message": "User not found"
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "code": 400,
        "message": "Login has failed, contact support team"
    }
```

**_ HTTP Code 422 - Unprocessable Entity - Validation error _**

```
    {
        "success": false,
        "code": 422,
        "message": "The email field must be a valid email address."
    }
```

#### Logout

[DELETE] /api/auth/logout
This endpoint makes user logout

Headers:

| Header        | Value            |
| ------------- | ---------------- |
| Accept        | application/json |
| Content-type  | application/json |
| Authorization | Bearer ...       |

Body:

| Property | Description | Required | Condition | Type |
| -------- | ----------- | -------- | --------- | ---- |

Responses:

**_ HTTP Code 201 - Created _**

```
    {
        "success": true,
        "code": 200,
        "message": "Logout successfully"
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "code": 400,
        "message": "Logout has failed, contact support team"
    }
```

### Wallet

#### Credit

[POST] /api/wallet/credit

This endpoint credit some amount in user's balance.

Headers:

| Header        | Value            |
| ------------- | ---------------- |
| Accept        | application/json |
| Content-type  | application/json |
| Authorization | Bearer ...       |

Body: JSON

| Property | Description      | Required | Condition | Type    |
| -------- | ---------------- | -------- | --------- | ------- |
| amount   | Amount to credit | yes      | ----      | Integer |

Responses:

**_ HTTP Code 201 - Created _**

```
    {
        "success": true,
        "code": 201,
        "message": "Amount credited successfully",
        "data": {
            "transaction_id": "abcde-1234-efhghz-123aszd"
        }
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "code": 400,
        "message": "Amount cannot be credited"
    }
```

**_ HTTP Code 422 - Unprocessable Entity - Validation error _**

```
    {
        "success": false,
        "code": 422,
        "message": "Amount cannot be less than 1"
    }
```

#### Debit

[POST] /api/wallet/debit

This endpoint debit some amount from user's balance.

Headers:

| Header        | Value            |
| ------------- | ---------------- |
| Accept        | application/json |
| Content-type  | application/json |
| Authorization | Bearer ...       |

Body: JSON

| Property | Description     | Required | Condition | Type    |
| -------- | --------------- | -------- | --------- | ------- |
| amount   | Amount to debit | yes      | ----      | Integer |

Responses:

**_ HTTP Code 201 - Created _**

```
    {
        "success": true,
        "code": 201,
        "message": "Amount debited successfully"
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "code": 400,
        "message": "Amount cannot be debited"
    }
```

**_ HTTP Code 422 - Unprocessable Entity - Validation error _**

```
    {
        "success": false,
        "code": 422,
        "message": "Amount cannot be less than 1"
    }
```

#### Balance

[POST] /api/wallet/balance

This endpoint returns user's balance.

Headers:

| Header        | Value            |
| ------------- | ---------------- |
| Accept        | application/json |
| Content-type  | application/json |
| Authorization | Bearer ...       |

Responses:

**_ HTTP Code 200 - Ok _**

```
    {
        "success": true,
        "message": "Balance retrieved succesffully",
        "code": 200,
        "data": {
            "amount": 10000
        }
    }
```

**_ HTTP Code 400 - Bad Request - An error has occurred _**

```
    {
        "success": false,
        "message": "An error has occurred",
        "code": 400
    }
```
