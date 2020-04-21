# TinyCache

Modern cache adapter for PHP Projects. 

- PHP ^7.0 Syntax
- PSR-4
- PSR-16
- Factory and Adapter Pattern
- Exception

## Supported Framework

This library is PSR-4 comply. You can use it on every framework that support composer.

- CodeIgniter 3/4
- Laravel
- Yii
- Your framework?
- Native PHP

## Supported Drivers

- Redis
- Memcached

Next plan : MongoDB, ApacheIgnite

## Installation

`composer require gemblue/tiny-cache`

## Usage

```php
use Gemblue\TinyCache\CacheFactory;

$cacheFactory = new CacheFactory;
$cache = $cacheFactory->getInstance('Memcached', 'localhost', '11211', true);
```

For full example, browse example folder.

## API

| Method | Desc |
--- | --- |
| set | Set a key |
| get | Get a key |
| delete | Delete a key |
| clear | Wipe all key |
| has | Check key existance |
| setMultiple | Set multiple key |
| getMultiple | Get multiple key |
| deleteMultiple | Delete multiple key |


