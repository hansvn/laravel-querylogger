# Laravel Query Logger

This package allows you to log and trace your queries.
For example: if you have some slow queries you can find where they are called.


## Requirements

- PHP >=5.4
- LARAVEL 4.2

## Getting started

### Laravel 4.2

1. Install the `brandworks/querylogger` package

    ```shell
    $ composer require brandworks/querylogger
    ```

2. Update app/config/app.php` to activate the package

    ```php
    # Add `Brandworks\Querylogger\QueryloggerServiceProvider` to the `providers` array
    'providers' => array(
        ...
        'Brandworks\Querylogger\QueryloggerServiceProvider',
    )

    # You do not need to add the alias - This is being handled for you in the service provider
    ```

3.  Publish the config file

    ```shell
    $ php artisan config:publish brandworks/querylogger
    ```

4.  Optionally, you can change the options

------------------

You can also add a route to your application to directly view your logs in a table

1. In the config: set the add_route and store_serialized options to true

    ```php
    # file: app/config/packages/brandworks/querylogger/config.php
    'store_serialized' => false,
    'add_route' => false,
    ```

2. You can optionally set the route you want to view your queries at

    ```php
    # file: app/config/packages/brandworks/querylogger/config.php
    'route' => 'queries',
    ```

> I don't recommend to enable this in a live environment;
> Your visitors can view your queries and deduct your database structure once they have found the route!

> So if you are to add the routes do this in a local config file. 
    

## Usage
Below is a simple usage example of this package

```php
//this does not affect user load since this is done in app finish (the client did already receive response)
App::finish(function($request, $response) {
    //make sure the package is enabled -- prevent errors
    if(class_exists("QueryLogger")) {
        $queryLog = QueryLogger::getLog();

        Log::info('The query log', $queryLog);
        //or do some other stuff with the log so you can make it more readale/filter on query time/...
    }
}
```


## Contributing

Contributions are welcome.

## Todo's

- Write tests

## Licence

This project is licensed under the terms of the MIT license.