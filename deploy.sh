#!/bin/bash
docker-compose up -d --build --force-recreate
docker exec php bash -c "php bin/console cron:start"
docker exec php bash -c "php bin/console doctrine:database:create"
docker exec php bash -c "symfony console doctrine:migration:migrate"

docker exec mysql bash -c "mysql -u root -ppassword < /scaffold/create_user.sql"
docker exec mysql bash -c "mysql -u root -ppassword < /scaffold/cron_job.sql"