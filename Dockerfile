FROM phpswoole/swoole:5.0-php8.2-alpine

ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.9.0/wait /wait
RUN chmod +x /wait

ADD ./.env /var/www/.env
ADD ./src /var/www/src
ADD ./vendor /var/www/vendor
ADD ./server.php /var/www/server.php
ADD ./bootstrap.php /var/www/bootstrap.php
ADD ./cache /var/www/cache
ADD ./config /var/www/config

ENTRYPOINT /wait && php server.php