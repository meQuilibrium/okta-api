# This must be compatible with stressys, okta-automations, and workforce-campaigns
FROM php:7.4.29

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        vim curl git wget unzip

# Composer (need 2.2 LTS for PHP < 7.2 support)
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/task
