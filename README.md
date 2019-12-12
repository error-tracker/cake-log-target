# [Error Tracker](https://error-tracker.com) Cake PHP Log Target

[![Build Status](https://travis-ci.org/error-tracker/cake-log-target.svg?branch=master)](https://travis-ci.org/error-tracker/cake-log-target)
[![Packagist](https://img.shields.io/packagist/dt/error-tracker/cake-log-target)](https://packagist.org/packages/error-tracker/cake-log-target)
[![Packagist Version](https://img.shields.io/packagist/v/error-tracker/php-sdk)](https://packagist.org/packages/error-tracker/cake-log-target)

## Who is this for?

This is for Cake PHP developers that need to integrate their applications with
[Error Tracker](https://error-tracker.com). This extension is a log adapter for
cake php to send errors and warnings direct to Error Tracker.

## Installation

Install this package with composer.

```bash
composer require error-tracker/cake-log-target
```

## Configuration

To configure the [log
target](https://book.cakephp.org/3/en/core-libraries/logging.html#logging-configuration)
in your application, add the below config to your `config/app.php`. Whenever
there is a server side error this will be added to the file log as it normally
would. Additionally this will be sent to the error tracker dashboard, for easy
searches, alerts and other handy tools.

```php
/**
 * Configures logging options
 */
'Log' => [
    'debug' => [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'debug',
        'levels' => ['notice', 'info', 'debug'],
        'url' => env('LOG_DEBUG_URL', null),
    ],
    'error' => [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'error',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'url' => env('LOG_DEBUG_URL', null),
    ],
    'error_tracker' => [
        'className' => 'ErrorTracker\Cake\LogTarget',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'app_key' => 'YOUR_APP_KEY',
    ],
],
```

## Contributing

### Getting set up

Clone the repo and run `composer install`.
Then start hacking!

### Testing

All new features of bug fixes must be tested. Testing is with phpunit and can
be run with the following command.

```bash
composer run-script test
```

### Coding Standards

This library uses psr2 coding standards and `squizlabs/php_codesniffer` for
linting. There is a composer script for this:

```bash
composer run-script lint
```

### Pull Requests

Before creating a pull request with your changes, the pre-commit script must
pass. That can be run as follows:

```bash
composer run-script pre-commit
```

## Credits

This package is created and maintained by [Practically.io](https://practically.io/)
