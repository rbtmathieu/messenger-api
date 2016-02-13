Api Symfony2 Project : *Le clan autonome des semi-croustillants*
=============

Workflow
--------

**Installation : **

> - Let's start with cloning the project in the dev environment 
> - `composer install` to install dependencies
> - The website is available by executing `web/app.php` for the prod environment, and `web/app_dev.php` for the dev environment

**Working on the project : **

> *A few rules before starting to code*
> 1. Prefer `[]` to build an array instead of `array()`
> 2. Always finish the last line of an array with a `,`
> 3. Split the code the best as you can, let it readable
> 5. Use labels on the PR, to let us know what we can expect
> 6. Never "insta-merge" a PR, excepted if it's a minimal modification or a hotfix
> 7. Never `git pull` without the `--rebase` (By default, it's merging, that can erase potential conflicts, and we don't want it)
> 8. Have fun

---------- 

> - `git pull --rebase` on master
> - create a new branch from master, with a name letting us understand what you're working on
> - Let's develop your feature, taking care of best practices and commenting if the code isn't clear enough 
> - Once the feature is done, you can commit the different parts of your feature, and then push on your branch
> - Open a pull request on master, and wait for at least **1** dev to approve your PR (We usually write :+1: (thumb) when it's ok
> - Take care to solve conflicts if there are some
> - When it's merged, get back to master and `git pull --rebase` again
> - Let's start again, be the best dev of the world and get cookies 

