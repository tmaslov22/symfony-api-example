symfony server:start
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load
Отправляем запросы через postman