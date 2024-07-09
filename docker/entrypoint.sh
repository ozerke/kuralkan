#!/bin/sh

# Start Supervisor
/usr/bin/supervisord -c /etc/supervisord.conf

# Start PHP-FPM in the foreground (this will be managed by Supervisor)
/usr/local/sbin/php-fpm -F
