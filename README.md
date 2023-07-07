# Wallet SOAP Service

## Environment Variables

Database: MySql

To run this project, you will need to add the following environment variables to your .env file

`URL_SERVER`=http://localhost:8091/server.php
`SERVER_DB`
`USERNAME_DB`
`PASSWORD_DB`
`NAME_DB`

How to install project:

1) Execute `composer install` in console to install dependencies
2) Execute `php .\generateWSDL.php` to run generate WSDL File
3) Execute `php .\migration.php` to run migration and create tables in database
4) Excute `php -S localhost:8091` to run service in port 8091