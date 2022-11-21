# **News Parser Symfony Application**

## Getting Started for the first time

Run the command below to start docker-compose

```
docker-compose up -d
```

After starting the docker containers we need to migrate the databases, tables and create default users(credentials below)

```
docker exec php bash -c "php bin/console doctrine:database:create"
docker exec php bash -c "php bin/console doctrine:migration:migrate"
docker exec mysql bash -c "mysql -u root -ppassword < /scaffold/create_user.sql"
```

Imports a cron job for symfony command (parseNewsFromApi) into mysql and starting the cron job (Default runs the command every minute)

```
docker exec mysql bash -c "mysql -u root -ppassword < /scaffold/cron_job.sql"
docker exec php bash -c "php bin/console cron:start"
```

Done!! the app should be running on [http://localhost:8080/login](http://localhost:8080/login) and login with these credentials

As an admin:

    - email => admin@gmail.com

    - password => qwertyuiop

As a moderator

    - email => mod@gmail.com

    - password => qwertyuiop

For the cronjob in order to change the schedule time you can update the existing cron job operation in mysql or create another job by running the command below

```
docker exec -it php bash
```

after getting into bash of the container run the command below and follow the instruction

```
symfony console cron:create
```
