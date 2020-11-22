## Digit88 ##

 - [Server Configuration](#markdown-header-server-configuration)
 - [Installation](#markdown-header-installation)
 - [Postman Collection](#markdown-header-postman-collection)

## Server Configuration
    PHP : 7.2.*
    MYSQL : 5.7.*
    APACHE : 2.2.*

## Installation

Step-by-step instructions (including commands used in terminal) to start the application.

Clone the Repo by using following cmd.

    git clone https://github.com/Miteshprmr/digit88.git

Create an environment file .env from .env.example with DB settings.
Give 777 permissions to directories within the storage and the bootstrap/cache.

Run the following commands in Terminal

    composer install
    php artisan key:generate
    php artisan migrate
    php artisan db:seed [only if seeded data is needed]
    php artisan passport:install

Add these env variables after oauth clients generated by passport:install command
take Laravel Password Grant Client type values

    FIRST_PARTY_APP_CLIENT_ID=
    FIRST_PARTY_APP_CLIENT_SECRET=

Run the following commands in Terminal

    php artisan cache:clear
    php artisan config:clear 
    php artisan config:cache

## Postman Collection

Postman Collection link for this application:
https://www.getpostman.com/collections/087831f9d75884aca4de

After importing the collection set env variable to your host server
example

    base_url=http://localhost:8000
