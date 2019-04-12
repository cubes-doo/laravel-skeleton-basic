# Laravel Skeleton (basic)

<p align="center">
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Version">
      <img src="https://img.shields.io/packagist/v/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Downloads">
      <img src="https://img.shields.io/packagist/dm/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
</p>

A Laravel starter project, for defining standards & kickoff speed :rocket:

*Read this in other languages: [English](README.md), [Srpski](README.sr-lat.md), [Српски](README.sr-cyr.md)*

## Getting Started

These instructions will get you a copy of the project and help you get it up and running on your local machine for development and testing purposes. See [deployment](#deployment) for notes on how to deploy the project on a live system.

### Prerequisites

1. [Composer](https://getcomposer.org/)
1. [Docker](https://docs.docker.com/install/)

### Installing

A step by step series of examples that will tell you how to get a development env running.

Firstly, we need to pull a project instance from packagist

```
composer create-project cubes-doo/laravel-skeleton-basic newProjectName
```

Now, if you want to use docker, first pull docker utillity files from our 'docker-compose' git repository:

```
git clone https://github.com/cubes-doo/docker-compose.git && rm -rf docker-compose/.git && mv docker-compose docker
```

Next, lets copy the `docker/.env.example` to a new `docker/.env` file

```
cp docker/.env.example docker/.env
```

Now, something more exciting :grin:, lets start our docker container!

```
sudo docker/docker-start.sh
```

Since there could be some discrepancies between UID's on different machines, lets run:

```
cd docker ; ./docker-usermod.sh ; cd ../
```

just in case. The short explanation for this is: using this command ensures that you have the same privileges as a user in the docker shell, as you would on your own machine.

Now we can run:

```
sudo docker/docker-console.sh
```

to enter the docker container's shell, and then inside it, run:

```
composer install
```

and you are good to go!

The [theme](https://coderthemes.com/codefox/menu-dark/index.html) used on this project, developed by [Coderthemes](https://coderthemes.com/), isn't free. So, at least visually, this project won't be useful to you without a paid licence. If you are one of our colleagues here at [Cubes](https://cubes.rs/), please contact one of your supperiors for a copy of the theme. Either way, the theme should be placed in the `public/theme/` folder.

Go ahead, log in and explore! By default, the project is exposed to http://localhost:7737. If this is no good for you, please refer to `docker/.env` & `.env` files to change it.

## Running code checks

Currently we are only using [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer), as a composer dependency. You'll find the ruleset at `.php_cs`. 

There are also two commands of note to make code checking easy:

```
sudo docker/docker-php-cs-check.sh
```
> does a dry-run of php-cs-fixer, only telling you what should be changed

and
```
sudo docker/docker-php-cs-fix.sh
```
> actually fixes the code using php-cs-fixer

### Automating code checks

To automate code checking, you could register git hooks that will run one of the commands mentioned above. We prefer the `commit` hook, but you could pick one that best suits you  :smile:

```
Give an example
```

## Running the tests

We created a script, that runs tests inside the Docker container:

```
sudo docker/docker-unit-test-run.sh
```

This script will run all exposed tests in the `tests/` folder.

## Deployment

Add additional notes about how to deploy this on a live system

## Reading Material

 - basics
    - [Laravel](https://laravel.com/docs/5.7)
    - [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
    - [Docker](https://docs.docker.com/get-started/)
 - for those in the know
    - [Simple Laravel TDD](https://medium.com/@jsdecena/simple-tdd-in-laravel-with-11-steps-c475f8b1b214)
    - [Easy to use app for .json based translations in Laravel](https://github.com/christofferok/langly)
    - [jQuery DataTables API for Laravel 4|5](https://github.com/yajra/laravel-datatables)
 - for the brave
    - [Sandi Metz - Code Smells & Refactoring (RailsConf2016)](https://www.youtube.com/watch?v=D4auWwMsEnY)
    - [RubyConf 2015 - How to Stop Hating your Test Suite by Justin Searls](https://www.youtube.com/watch?v=VD51AkG8EZw)
    - [Justin Searls - Breaking up (with) your test suite (Ancient City Ruby 2014)](https://www.youtube.com/watch?v=9_3RsSvgRd4)
    - [Justin Searls - The Failures of "Intro to TDD"](http://blog.testdouble.com/posts/2014-01-25-the-failures-of-intro-to-tdd)

## Built With

* [Composer](https://getcomposer.org/) - PHP package management system
* [Laravel 5.8](https://laravel.com/docs/5.8/) - The web framework used
* [Docker](https://docs.docker.com/) - Containerization platform

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/cubes-doo/laravel-skeleton-basic/releases). 

## Authors

* **Aleksandar Dimic** - *Chief Science Officer* - [aleksandar-dimic](https://github.com/aleksandar-dimic)
* **Aleksa Cvijić** - *Developer* - [acvi](https://github.com/ACvijic)

See also the list of [contributors](https://github.com/cubes-doo/laravel-skeleton-basic/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc

