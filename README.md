# Connect with the Brick framework

The `enflow/component-brick` package provides the logic to connect to the iOS and Android side of the Brick framework. **Brick is meant to be used as the server-side variant of the Brick framework. See "Whats Brick"**

## What's Brick
Brick is Enflow's framework to create hybrid iOS and Android apps. These wrappers communicate with the server side trough a JavaScript bridge. This package writes a tag to your application that those native wrappers communicate with. The primary function of this package is to support push notifications by fetching the device ID from the device and write it to a table.

## Installation
You can install the package via composer:

``` bash
composer require enflow/component-brick
```

### Migrations
This package includes a migration that needs to be published. This table (`brick_devices`) includes the mapping between the user and the hardware UUID of the iOS or Android device required for push notifications. New devices will be automatically assigned to the user. You can publish this migration by running:

`php artisan vendor:publish --provider="Enflow\Component\Brick\BrickServiceProvider"`

## Usage
This package adds the `BrickManager` class to the container and injects a `$brickManager` variable to all views. This variable can be used to render the required tag automatically. We recommend adding this to the end of the master template, just before the `</body>`:
`{!! $brickManager->tags() !!}`

We recommend viewing through the files in this package to understand it's usage and logic.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email michel@enflow.nl instead of using the issue tracker.

## Credits
- [Michel Bardelmeijer](https://github.com/mbardelmeijer)
- [All Contributors](../../contributors)

## About Enflow
Enflow is a digital creative agency based in Alphen aan den Rijn, Netherlands. We specialize in developing web applications, mobile applications and websites. You can find more info [on our website](https://enflow.nl/en).

