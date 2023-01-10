This is a tiny microframework for building APIs based on
Swoole, FastRoute, Doctrine DBAL, Doctrine Migrations,
and PHP-DI. It's a work in progress.

Getting Started
---------------

You'll need PHP 8.1, make, curl and a recent version of 
docker + docker-compose. Then, just do:

```sh
$ make               # run composer, download extras, etc
$ make test          # run unit tests
$ docker-compose up  # run it
```

`make` will tell you if you don't have a required command,
but it will not check for the correct versions.

The first start will take a little longer than usual, the
following ones should reuse a lot of docker images and
caching.

Troubleshooting
---------------

There are some additional commands that might help you out:

```sh
$ make clean         # Clears caches, removes vendors, etc
$ make distclean     # make clean + clear local configs
$ make vendor        # Re-run the composer routines
```

Check the `Makefile` for other internal commands.

There are just a few files and folders you should worry
about:

```sh
./.env                # Environment Variables
./config/config.php   # Dependeny Injection + Routing
./src/Routes          # Route targets
./src/Migrations      # Doctrine Migrations
```

Additional Information
----------------------

The following files are internal and there is no need to
edit them unless you're adding a new core component:

```sh
./bootstrap.php          # Bootstraps autoload + PHP-DI
./config/cli-config.php  # Doctrine Migrations runner
./config/migrations.php  # Doctrine Migrations configuration
./Dockerfile             # Swoole docker image + wait script
./server.php             # Swoole entrypoint
./phpunit.xml            # PHPUnit Configuration
./src/Application.php    # Router on top of FastRoute
./docker-compose.yml     # Stack of service dependencies
```

Versions used for development:

  - PHP 8.1.13 (cli)
  - curl 7.87.0
  - Docker version 20.10.22
  - Docker Compose version 2.14.2
  - Linux dev 6.1.1-1 (Manjaro 22.0.0)

The TODO List
-------------

 - Logging. We are using error_log() that defaults to
   outputting to the stderr of the container, but that's
   not ideal.
 - Caching.
 - Monitoring.
 - Leverage DBAL and support sakila for other DBMSs.
 - Security Hardening. This should not be used in production.
 - Better comments and internal API doc comments.
 - PHP 8.2 support.
 - `make unsakila` to remove the sakila parts and leave
   just the framework skeleton.
 - More testing.