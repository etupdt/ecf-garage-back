# ecf-garage-back

## Create database

- Install Mariadb and create a database and a application user with admin account

```
CREATE DATABASE ecfgarage;
CREATE USER 'ecfgarage'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON 'ecfgarage'.'localhost' TO 'ecfgarage'@'localhost';
```

## Local install

- Go to project directory
```
cd ecf-garage-front
```  

- install php : On Windows, download php installer and run it. Put path in environment variables. 
  
- install composer and start database migration
```
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migration:migrate
```

## Create JWT Token keys

- Create jwt directory

```
mkdir config/jwt
```

- Create private en public keys for JWT token. The passphrase will be to add to .env.local file

```
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

## Create .env.local file :

- In the project directory create a .env.local file with these variables

```
APP_ENV=dev
DATABASE_URL="mysql://ecfgarage:password@localhost:3306/ecfgarage?charset=utf8mb4"

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_jwt_keys_passphrase
```

- Optionnal mail server parameters : If you want use the password reinit fonctionnality, you must add the MAILER_DSN variable in the .env.local file. Here is a Mailtrap example where you must change 'mail-user' and 'mail_password' with you mailtrap credential.

```
MAILER_DSN=smtp://user_password:mail_password@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
```

## Start development server

- Run server application

```
symfony server:start
```
