# Transaction Processor

## Before run:
1. make sure you have PHP installed.
2. `composer install` to install dependencies.
3. Set-up exchange-rates access key in the `config/config.php` file
4. If you have `BinList` service credentials for an access - adopt the code for considering this. Currently the response from this service is mocked.

## Run:
1. Use `php app.php input.txt` to run transaction processing.

## Run unit tests:
1. Make sure you commented mocks in Clients and uncommented executions of `$this->client...` in the code. The lines are marked by comments in the clients.
2. execute `./vendor/bin/phpunit tests/`
