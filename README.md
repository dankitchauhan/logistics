
# Logistics API

Rest API's for creating updating and listing orders.

## Tech Stack used...

- [Php v7.2](https://php.net/) to develop backend support.
- [MySQL v5.7](https://mysql.com/) as the database.
- [Laravel v6.2](https://laravel.com/docs/6.x) Laravel is a web application framework, created by Taylor Otwell.
- [Docker](https://www.docker.com/) as the container service to isolate the environment. 
	- [Laradock](https://laradock.io/) Laradock is a full PHP development environment based on Docker.
- [Nginx](https://www.nginx.com/) as web server
- [PHPUnit](https://github.com/sebastianbergmann/phpunit) for Unit and Integration Testing
- [Swagger 6.0](https://github.com/DarkaOnLine/L5-Swagger) Swagger is an open-source software framework backed by a large ecosystem of tools that helps developers design, build, document, and consume RESTful Web services.

## How to start

### \*NOTES: Before running with Docker, it assumes that Docker environement pre-installed in your system.

1). Clone GIT repository in your projects folder

```bash
git clone https://github.com/ankit246/logistics
cd logistics/
```

2). Open .env file and Set Google location API key.

```bash
GOOGLE_KEY=
```

3). Open Command Line And Run start.sh shell script.

```bash
./start.sh
```
** Now your project is up and running **
** Note: If project installation fails check permissions and run the script again **

# Running Test cases...

## Run all test cases

```bash
docker-compose exec workspace ./vendor/bin/phpunit
```
Or
```bash
cd laradock/
docker-compose exec workspace bash
phpunit
```

## To Run only unit test cases

With Docker

```bash
docker-compose exec workspace ./vendor/bin/phpunit --testsuite Unit
```
Or
```bash
cd laradock/
docker-compose exec workspace bash
phpunit --testsuite Unit
```

## To Perform Integration test cases

With Docker

```bash
docker-compose exec workspace ./vendor/bin/phpunit --testsuite Feature
```
Or
```bash
cd laradock/
docker-compose exec workspace bash
phpunit --testsuite Feature
```
## API's Reference

#### Place order

- Description: Create a new Order.
- Method: `POST`
- URL path: `http://localhost/orders`
- Content-Type: `application/json`
- Request body:

  ```
  {
      "origin": ["START_LATITUDE", "START_LONGTITUDE"],
      "destination": ["END_LATITUDE", "END_LONGTITUDE"]
  }
  ```

- Response:

  Header: `HTTP 200`
  Body:

  ```
  {
      "id": <orderId>,
      "distance": <distance>,
      "status": "UNASSIGNED"
  }
  ```

  or

  Header: `HTTP <HTTP_CODE>`
  Body:

  ```
  {
      "error": "ERROR_DESCRIPTION"
  }
  ```

  ```
    Code                    Description
    - 200                   successful operation
    - 400                   Bad Request
    - 405                   Method Not Allowed
    - 422                   Request Body Validation Error
    - 500                   Internal Server Error
  ```

#### Take order

- Description: Update/take a new Order.
- Method: `PATCH`
- URL path: `http://localhost/orders/:id`
- Content-Type: `application/json`
- Request body:
  ```
  {
      "status": "TAKEN"
  }
  ```
- Response:
  Header: `HTTP 200`
  Body:

  ```
  {
      "status": "SUCCESS"
  }
  ```

  or

  Header: `HTTP <HTTP_CODE>`
  Body:

  ```
  {
      "error": "ERROR_DESCRIPTION"
  }
  ```

  ```
    Code                    Description
    - 200                   successful operation
    - 400                   Bad Request
    - 405                   Method Not Allowed
    - 422                   Request Body Validation Error
    - 500                   Internal Server Error
  ```

#### Order list

- Description: List/get Order List.
- Method: `GET`
- URL path: `http://localhost/orders`
- Content-Type: `application/json`
- Response:
  Header: `HTTP 200`
  Body:

  ```
  [
      {
          "id": <orderId>,
          "distance": <distance>,
          "status": <ORDER_STATUS>
      },
      ...
  ]
  ```

  or

  Header: `HTTP <HTTP_CODE>` Body:

  ```
  {
      "error": "ERROR_DESCRIPTION"
  }
  ```

  ```
  Code                    Description
    - 200                   successful operation
    - 400                   Bad Request
    - 405                   Method Not Allowed
    - 422                   Request Body Validation Error
    - 500                   Internal Server Error
  ```
  
## For API's documentation

1. Visit `http://localhost/api/documentation` for API documentation

## Check code-coverage using blow URL

`http://localhost/code-coverage/`


## Credits

- [Ankit Chauhan](https://github.com/ankit246/logistics)

- For Docker Implementation get help from
  [https://docs.docker.com/docker-for-windows/install/](https://docs.docker.com/docker-for-windows/install/)
  
-	For PHP development environment [https://laradock.io/](https://laradock.io/)

- For Swagger Integartion get help from
  [https://github.com/DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)

- And obviously (Stack Overflow)
  (https://stackoverflow.com)
