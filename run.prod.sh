#!/bin/bash
chmod 777 .env
chown www-data:www-data .env

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion

FILE=/etc/cron.d/kuralkan-prod
if test -f "$FILE"; then
    echo "$FILE crontab already exists."
else
    touch $FILE
    echo "* * * * * root docker exec -t kuralkan-prod php artisan schedule:run >> /dev/null 2>&1\n" >> $FILE
    echo "$FILE was created and filled for cron jobs."
    service cron reload
fi

nvm use 18.18

npm i
npm run build

docker-compose -f ./docker/docker-compose-prod.yml build
docker-compose -f ./docker/docker-compose-prod.yml up -d

# Wait for services to be up
sleep 30

docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-prod php artisan migrate --force
docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-prod php artisan key:generate --force
docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-prod php artisan optimize:clear
docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-prod php artisan optimize
docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-nginx-prod rm /var/www/html/public/storage
docker-compose -f ./docker/docker-compose-prod.yml exec -T kuralkan-nginx-prod ln -s /var/www/html/storage/app/public /var/www/html/public/storage
