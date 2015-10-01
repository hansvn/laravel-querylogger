# Laravel Query Logger

This package allows you to log and trace your queries.
For example: if you have some slow queries you can find where they are called.


## Requirements

- PHP >=5.4
- LARAVEL 4.2

## Getting started
------------------

### Laravel 4.2

1.  Install the `brandworks/querylogger` package

    ```shell
    $ composer require brandworks/querylogger
    ```

1. Update app/config/app.php` to activate the package

    ```php
    # Add `Brandworks\Querylogger\QueryloggerServiceProvider` to the `providers` array
    'providers' => array(
        ...
        'Brandworks\Querylogger\QueryloggerServiceProvider',
    )

    # You do not need to add the alias - This is being handled for you in the service provider

1.  Publish the config file

    ```shell
    $ php artisan config:publish brandworks/querylogger
    ```

1.  Optionally, you can change the options


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
