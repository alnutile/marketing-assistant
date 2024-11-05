# Project Assistant 
This is the code used in the "PHP and LLMs" book long with [https://github.com/alnutile/php-llms](https://github.com/alnutile/php-llms)

This is a full working system with Automations, Projects, Tasks and more!

![CleanShot 2024-10-23 at 20 16 44@2x](https://github.com/user-attachments/assets/060135b6-57a1-4387-be4b-d49bcbd04852)



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

