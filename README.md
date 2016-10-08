Payment API Demo
----------------

This is just an experimental project.

## Set up

[Composer](https://getcomposer.org/) is required.

```bash
composer install
```

Then configure the database details in `config/config.ini`.

## Migrating the database

```bash
php console db:reset
php console db:migrate
php console db:seed
```

## Web server set up

An example nginx config file is shown in `config/nginx.conf`.

## Running tests

```bash
phpunit --testsuite unit
phpunit --testsuite functional
```

Note that the functional tests reset the database for each test.

## Payment provider requests

The payment provider can make POST requests against this endpoint in JSON
format:

```
/restaurant-chains/123/locations/456/payments
```

These requests must have an `X-Signature` header containing an SHA-256 HMAC of
the request body signed with a pre-defined signing key. The default testing key
is "foobar".

The format of the request body for posting payments is:

```json
{
    "provider": 123,
    "approved_at": "2016-12-12T07:01:55+00:00",
    "total_amount": 2020,
    "items": [
        {
            "description": "A starter",
            "amount": 350
        },
        {
            "description": "A main course",
            "amount": 1200
        },
        {
            "description": "A drink",
            "amount": 550
        },
        {
            "description": "Special discount",
            "amount": -100
        },
        {
            "description": "Gratuity",
            "amount": 20
        }
    ],
    "table_no": 5,
    "served_by": {
        "name": "Joe Bloggs"
    },
    "user": 456,
    "device": {
        "operating_system": "Android 7.1",
        "model": "Some Phone 5"
    },
    "method": {
        "organisation": "visa",
        "last_4_digits": "4242",
        "fraud_risk": "low"
    }
}
```

An example request for posting a new payment is:

```
GET /restaurant-chains/123/payments HTTP/1.1
Host: localhost
X-Signature: 08315dfa59f8a2ef660ee5e89e3ab357ba87df5d796962c977c89396dfa2c345

{"provider":123,"approved_at":"2016-12-12T07:01:55+00:00","total_amount":2000,"items":[{"description":"A starter","amount":350},{"description":"A main course","amount":1200},{"description":"A drink","amount":550},{"description":"Special discount","amount":-100},{"description":"Gratuity","amount":20}],"table_no":5,"served_by":{"name":"Joe Bloggs"},"user":456,"device":{"operating_system":"Android 7.1","model":"Some Phone 5"},"method":{"organisation":"visa","last_4_digits":"4242","fraud_risk":"low"}}
```

## Restaurant payment report requests

Restaurant staff members can make requests for payment reports about the whole
chain or individual locations.

Staff members are presumed to make requests via a user agent that adds an
`Authorization` header, e.g. `Basic Nzg5OnNlY3JldA==` for account `789` with
password `secret`.

The API will respond with the report in JSON format. Again, the user agent has
the responsibility of rendering this for the end-user.

An example request for a report of payments to the whole chain is:

```
GET /restaurant-chains/123/payments HTTP/1.1
Host: localhost
Authorization: Basic Nzg5OnNlY3JldA==
```

And for an individual location:

```
GET /restaurant-chains/123/locations/456/payments HTTP/1.1
Host: localhost
Authorization: Basic Nzg5OnNlY3JldA==
```
