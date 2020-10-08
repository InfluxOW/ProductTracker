![Main workflow](https://github.com/InfluxOW/StockTrackerConsoleApp/workflows/Main%20workflow/badge.svg)
[![codecov](https://codecov.io/gh/InfluxOW/StockTrackerConsoleApp/branch/master/graph/badge.svg)](https://codecov.io/gh/InfluxOW/StockTrackerConsoleApp)

# Product Tracker Console App
It allows you to track items from online shops (only BestBuy is included, but it's extendable).
It based on [Laracasts.com](https://laracasts.com/series/build-a-stock-tracker-app) course but hugely extended with a lot of commands.

# Development Setup
1. Run `make setup` to install dependencies, generate .env file, create SQLite database, apply migrations and etc.
Add your BestBuy Api Key to `.env` file!
2. Run `make seed` to create BestBuy retailer with Nintendo Switch product to test tracking.
3. Run `make test` to execute tests.
4. run `php artisan | grep tracker` to view available commands.
