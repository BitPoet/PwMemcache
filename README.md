# PwMemcache
ProcessWire interface to PHP memcache

## Purpose
This module provides a simple interface to PHP's [Memcache](https://www.php.net/manual/de/book.memcache.php) library for caching contents in-memory. The functionality leans on [ProcessWire](https://processwire.com/)'s WireCache core library.

## Compatibility
Compatible with ProcessWire 3.x

## Status
Beta, use with care

## Prerequisites
The Memcache extension must be installed and enabled in PHP.

#### Notice
There are two quite similar libraries, Memcache and Memcache*d*. The former is the one this module works with. It may be a starting point for anybody wanting to create a similar module for Memcache*d*.

## Usage

### Setup

After installation, you need to enable the module in the module settings by ticking the "Active" checkbox. The server and port are preset to 127.0.0.1:11211, but you can change that and add any number of servers if you are operating a cluster.

### Basics

PwMemcache registers itself as a PW API variable with the name "memcache", so you can access in all the usual ways:
```PHP
$var = $memcache->get('mykey');
$var = wire('memcache')->get('otherkey');
$var = $this->memcache->get('thirdkey'); // only within Wire derived classes
```

### Methods

#### $memcache->cget($name, $expire, $func)

Retrieve a value from the cache by the given name. If $func is given and no cached entry was found, call $func and use its return value instead, saving that in the cache with the given expiry time.

Alternatively, you can pass an array of names to the method. In that case, an associative array of names and values is returned, with only those keys present that were found in the cache. If you do that, you must not pass a $func parameter.

```PHP
$stats = $memcache->cget($name, 600, function() use($pages) {
	return "<span class='status'>Site has" . $pages->count() . " pages</span>";
});
```

#### $memcache->cset($name, $expire, $value)

Save the value in the cache with the given name as the key and the specified expire time. If $expire is not set, PwMemcache::expireDaily is assumed (24h).

```PHP
// Save a string value in the cache (here the HTML of a rendered page):
$memcache->cset('nav', 600, $pages->get('/navigation/')->render());
// Save an array value:
$orders = [];
foreach($pages->find('template=order') as $order) {
  $orders[$order->id] = $order->sum;
}
$memcache->cset('orders', 300, $orders);
```

#### $memcache->delete($name)

Deletes the entry with the given key name from the cache.

#### $memcache->flush()

Delete all entries from the cache.

#### $memcache->renderFile($filename, $expire, _array_ $options)

This method behaves similar to ```$files->render()``` and actually delegates the file rendering to that method (when creating the cache). The important difference is that this method caches the output according to the given expiry value, rather than re-rendering the file on every call.

If there are any changes to the source file `$filename` the cache will be automatically re-created, regardless of what is specified for the `$expire` argument.

```PHP
// render primary nav from site/templates/partials/primary-nav.php
// and cache for 3600 seconds (1 hour)
echo $memcache->renderFile('partials/primary-nav.php', 3600);
```

*Parameters for renderFile:*

- string `$filename`
  + Filename to render (typically PHP file)
  + Can be full path/file, or dir/file relative to current work directory (which is typically /site/templates/).
	+ If providing a file relative to current dir, it should not start with "/". 
	+ File must be somewhere within site/templates/, site/modules/ or wire/modules/, or provide your own `allowedPaths` option. 
	+ Please note that `$filename` receives API variables already (you don’t have to provide them).

- int|string `$expire`
	 - Specify one of the `PwMemcache::expire*` constants.
	 - Specify the future date you want it to expire (as unix timestamp )
	 - Specify `PwMemcache::expireNever` to prevent expiration.
	 - Specify `PwMemcache::expireSave` to expire when any page or template is saved.
	 - Omit for default value, which is `PwMemcache::expireDaily`. 

- array `$options`
  Accepts all options for the `WireFileTools::render()` method, plus these additional ones:
    - `name` (string): Optionally specify a unique name for this cache, otherwise $filename will be used as the unique name. (default='')
    - `vars` (array): Optional associative array of extra variables to send to template file. (default=[])
    - `allowedPaths` (array): Array of paths that are allowed (default is anywhere within templates, core modules and site modules)
    - `throwExceptions` (bool): Throw exceptions when fatal error occurs? (default=true)

*Return*

string|bool Rendered template file or boolean false on fatal error (and throwExceptions disabled)

## License

Licensed under Mozilla Public License v2. See file LICENSE in the repository for details.

## Credits

Ryan Cramer, creator of ProcessWire. Quite a bit of his well thought out code was taken more or less literally from his WireCache class to keep things compatible and keep from reinventing the wheel.
    
