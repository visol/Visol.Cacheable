Cacheable
=========

This Flow Framework package allows caching the return value of Flow Framework methods through annotations.
                            
## Features

* Configure the lifetime
* Configure cache tags
* Optionally protect cache entries with the currently authenticated account or the roles associated to the currently authenticated
* Use a persistent or a transient cache 
* This is built on top of the Flow Framework. The caching configuration can be adjusted based on the project's needs.

## Installation

To get the latest version of Cacheable, require the project using [Composer](https://getcomposer.org):

```bash
composer require visol/cacheable
```

## Usage

For annotations to work, make sure to import the respective namespaces in your class:

```php
use Visol\Cacheable\Annotations as Cacheable;
use Visol\Cacheable\Annotations\Method;
```

You can now add the annotation to methods whose results you want to be cached:

```php
/**
 * @return array
 * @Cacheable\Method(lifetime=1800, tags={"rest_service"}, security=Method::SECURITY_ROLES, cacheIdentifier=Method::CACHE_PERSISTENT)
 */
public function executeLongRunningTask(string $someArgument): array
{
    // Fetch data from API or perform long running calculations
}
```

This would cache the return value for 30 minutes in the persistent cache, taking into account all roles of the currently authenticated user, tagged with rest_service.

Warning: Only use this annotation for static or static like methods. E.g. fetching external data or performing calculations depending solely on supplied parameters.

### Cache lifetime configuration

You can configure the cache lifetime in seconds:

    lifetime=1800
    
This will cache your data for 30 minutes.

### Cache tags configuration

You can configure an array of cache tags:

    tags={"tag_1", "tag_2"}

This allows you to programmatically flush the cache in your code.

### Security method configuration 

The cached data can either be bound to the currently authenticated account or to the roles of the currently authenticated account.

**No protection _(default)_:**

    security=Method::SECURITY_NONE

**Account:**

    security=Method::SECURITY_ACCOUNT

**Roles:**

    security=Method::SECURITY_ROLES

### Cache type

By default, two caches are configured:

**Persistent cache _(default)_:**

    cacheIdentifier=Method::CACHE_PERSISTENT
    
Stores the data as persistent data in the `FileBackend`.

**Transient cache:**

    cacheIdentifier=Method::CACHE_TRANSIENT
    
Stores the data in the `TransientMemoryBackend`.

If you need to adjust configuration to your needs, override the caches in your application package's or distribution's `Caches.yaml`. See `Configuration/Caches.yaml` for the default configuration.

### Cache Entry Identifier

A cache entry identifier is automatically generated based on namespace, arguments and security settings.

By default the namespace is set to the class and method name.

All supplied arguments are used as is. Be cautious when using complex objects.

## Credits

visol digitale Dienstleistungen GmbH, www.visol.ch

Inspired by:

* Annotation Type Cacheable in Spring Framework
* Python function caching
* [Packagist package yateric/cacheable](https://packagist.org/packages/yateric/cacheable)

## License

Cacheable is licensed under [The MIT License (MIT)](LICENSE).
