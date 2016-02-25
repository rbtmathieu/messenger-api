Api Symfony2 Project : *Le clan autonome des semi-croustillants*
=============

Workflow
--------

**Installation :**

> - Let's start with cloning the project in the dev environment 
> - `composer install` to install dependencies
> - `php app/console doctrine:database:create` to create the database
> - `php app/console doctrine:migrations:migrate` to update your databaase
> - The website is available by executing `web/app.php` for the prod environment, and `web/app_dev.php` for the dev environment

**Working on the project :**

> *A few rules before starting to code*

> - Prefer `[]` to build an array instead of `array()`
> - Always finish the last line of an array with a `,`
> - Split the code the best as you can, let it readable
> - Use labels on the PR, to let us know what we can expect
> - Never "insta-merge" a PR, excepted if it's a minimal modification or a hotfix
> - Never `git pull` without the `--rebase` (By default, it's merging, that can erase potential conflicts, and we don't want it)
> - Have fun

---------- 

> - `git pull --rebase` on master
> - create a new branch from master, with a name letting us understand what you're working on
> - Let's develop your feature, taking care of best practices and commenting if the code isn't clear enough 
> - If you've made changes in database scheme, generate the migration diff with `php app/console doctrine:migrations:diff`and don't forget to commit the file
> - Once the feature is done, you can commit the different parts of your feature, and then push on your branch
> - Open a pull request on master, and wait for at least **1** dev to approve your PR (We usually write :+1: (thumb) when it's ok
> - Take care to solve conflicts if there are some
> - When it's merged, get back to master and `git pull --rebase` again
> - Let's start again, be the best dev of the world and get cookies 

----------

> *Launching fixtures*
> - `php app/console doctrine:database:drop --force` (If you have data in your database you want to keep, make a back up before executing this)
> - `php app/console doctrine:database:create` 
> - `php app/console doctrine:migration:migrate` 
> - `php app/console hautelook_alice:doctrine:fixtures:load` 

----------

> *Launching tests*
> - create the file `app/phpunit.xml` from `app/phpunit.xml.dist`
> - `phpunit -c app/phpunit.xml` if you installed phpunit globally
> - `vendor/phpunit/phpunit/phpunit -c app/phpunit.xml` if you didn't

