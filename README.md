# serve2

This contraption can be used as an alternative to the PHP built-in web server in scenarios where you need more than one connection but for some reason can't install a real web server like NGINX, Apache, Lighttpd or Caddy.

__NEVER USE IT IN PRODUCTION.__

Not tested on Windows yet.


## Installation as a standalone command

It's distributed as a PHAR executable, just like composer.

```sh
wget https://github.com/carlosvini/serve2/releases/download/v0.1.0/serve2.phar -O /usr/local/bin/serve2
chmod +x /usr/local/bin/serve2
```

### How to run

```sh
cd /my-folder-with-php-code
serve2 

# OR:

serve2 --port 8000
```

## Installation as a Laravel command

If you have auto-discover enabled:

```sh
 composer require carlosvini/serve2
```

Otherwise, manually add "CarlosVini\\Serve2\\Serve2ServiceProvider" to config/app.php, in the "providers" array.


### How to run

```sh
php artisan serve2
```

## How it works

It opens PHP servers and forwards the requests to any of them which is less busy.
It tries to close the servers on shutdown but it might fail since it's a hack right now. In this case run "pkill php" to kill the unclosed servers.
