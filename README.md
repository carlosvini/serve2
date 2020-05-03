# serve2

This contraption can be used as an alternative to the PHP builtin webservice in scenarios where you need more than one connection but for some reason can't install a real web server like NGINX, Apache, Lighttpd or Caddy.

__NEVER USE IT IN PRODUCTION.__

Not tested on Windows yet.


## Installation

It's distributed as a PHAR executable, just like composer.

```sh
wget https://github.com/carlosvini/serve2/releases/download/v0.1.0/serve2.phar -O /usr/local/bin/serve2
chmod +x /usr/local/bin/serve2
```

## Run

```sh
cd /my-folder-with-php-code
serve2 

# OR:

serve2 --port 8000
```
