FROM php:8.1-fpm-bullseye

RUN apt-get update && apt-get install -y supervisor

COPY ./infra/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]
