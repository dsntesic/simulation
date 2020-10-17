# Project Battle Simulator

Hi,
I create the project in Laravel framework,

### Prerequisites

1. [Composer](https://getcomposer.org/)
2. [Docker], [Xampp] or other local web server platform

### Installing

A step by step series of examples that will tell you how to get a development env running.

Firstly, we need to pull a project instance from my github account
```
git clone https://github.com/dsntesic/simulation.git
```

Next we need to set local server.

I used Xampp:

in file hosts I added 
```
127.0.0.1       simulation.local
```
then in file httpd-vhosts.conf i added
```
<VirtualHost *<VirtualHost *:80>
    ServerName simulation.local
    DocumentRoot "path_to_project_public_folder"
    <Directory "path_to_project_public_folder">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order deny,allow
        Require all granted
    </Directory>
</VirtualHost>
```
then run xampp server and go to local phpmyadmin and create new database with name simulator.

Next, lets copy the `.env.example` to a new `.env` file

```
cp .env.example .env
```

Run

```
composer install
```

in file .env in root check if APP_KEY has value (exmple: 'APP_KEY=base64:uF6WCsZNZP2JVZC5VaDvEtqutd1Y0YtROp8GfG95ttw='), if it doesn't also run:

```
php artisan key:generate
```
also in .env file please set if you use different local web server

APP_URL={local TLD}
DB_HOST={local TLD}

For making your local database you should run:

```
php artisan migrate
```

if running app in web browser throw exception for permission denied for storage folder run 
```
sudo chmod -R 777 ./storage
```
