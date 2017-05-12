Deployment Document
===================

-----Vereisten-----
Apache/ngnix
PHP >= 5.6.4
OpenSSL PHP Extension
PDO PHP Extension
Mbstring PHP Extension
Tokenizer PHP Extension
XML PHP Extension

MYSQL database met user en wachtwoord

git 
composer https://getcomposer.org/
npm https://docs.npmjs.com/getting-started/installing-node

-----Install-----
In command, ga naar map waar webserver moet komen

---Voer uit---
git init .
git remote add origin https://github.com/SiebeVE/keuzetool-om.git
git pull origin master
composer install
npm install
cp .env.example .env
php artisan key:generate

---Pas Aan in .env---
Verander nu in het .env bestand volgende zaken
APP_URL=https:// -- Url van de applicatie (https://...)
DB_DATABASE=db -- De naam van de databse
DB_USERNAME=user -- De gebruikersnaam van de database
DB_PASSWORD=pass -- Het wachtwoord van de database

---Voer uit----
php artisan migrate --seed
	Admin kan nu aanmelden via /admin
	username: admin@kdg.be
	password: LaSePiSi17!

---Pas Aan in .env---
APP_ENV=local => APP_ENV=production

De applicatie is nu klaar

----LET OP----
De documentRoot in uw apache/ngnix config file moet naar de /public folder verwijzen
