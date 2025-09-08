Install db, phpma, composer and mailhog with docker compose.
>> docker compose run --rm composer install
>> On host PC : php bin/console importmap:install
>> On host PC : php bin/console doctrine:migration:migrate
>> build app avec docker compose