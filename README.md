# RTM Pakcage


The RTM Package is a Laravel package designed to streamline RTM (Requirement Traceability Matrix) management for the projects that we are building. It includes a set of  artisan commands that helps in seamless export and organization of test artifacts like screenshots and GIFs into timestamped ZIP archives.



## Installation

### Step 1:Download and Extract the Package 

Download the package as a zip file and extract it into your Laravel project’s ``packages/`` directory under the name rtmThe resulting folder structure should look like this:

```
    laravel-project/
        ├── app/
        ├── config/
        ├── packages/
        │   └── rtm/
        │       ├── src/
        │       │   ├── Commands/
        │       │   ├── Stubs/
        │       │   └── Config/
        │       ├── composer.json
        ├── ...
```

### Step 2: Update composer.json
Add the package to your Laravel application as a path repository:

 1.Open your project’s composer.json file and add the following: 
 ```
    "repositories": {
        "icrewsystems/rtm": {
            "type": "path",
            "url": "packages/rtm",
            "options": {
                "symlink": true
            }
        }
    },
    "require": {
        "icrewsystems/rtm": "@dev",
        "ext-imagick": "*",
        "ext-zip": "*"
    }
 ``` 
 2. Run Composer to install the package:
 ``` 
   composer require icrewsystems/rtm
 ```

 3. Update Composer’s autoloader:
 ```
    composer dump-autoload
 ```
    
## Usage

```php
// Usage description here
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email thirumalai.raj@icrewsystems.com instead of using the issue tracker.

## Credits

-   [Thirumalai](https://github.com/icrewsystems)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
