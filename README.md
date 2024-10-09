# Project Assistant 


This is the code used in the "PHP and LLMs" book long with [https://github.com/alnutile/php-llms](https://github.com/alnutile/php-llms)


## Setup

Should setup like a normal Laravel project

but a few things to consider:

### Database 
cp you .env.example to .env and fill in the DB_ variables

but keep in mind you have to make the Postgres db ahead of time
Laravel will not create the db for you

Use HERD or DBNgine and all will go well for Postgres

### Seed Admin 

cp you .env.example to .env and fill in the ADMIN_EMAIL and ADMIN_PASSWORD

```bash
php artisan db:seed --class=AdminSeeder
```

## Learn more at 
 
  * 👉🏻 Buy the book "PHP and LLMs - the practical guide" https://bit.ly/php_llms
  * 👉🏻 Join the NewsLetter https://sundance-solutions.mailcoach.app/php-and-llms

