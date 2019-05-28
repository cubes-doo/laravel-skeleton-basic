# Laravel Skeleton (basic)

<p align="center">
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Version">
      <img src="https://img.shields.io/packagist/v/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Downloads">
      <img src="https://img.shields.io/packagist/dm/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
</p>

Ларавел стартер пројекат, за дефинисање стандарда и брзину покретања :rocket:

*Прочитајте ово на другим језицима: [English](README.md), [Srpski](README.sr-lat.md)*

## Почетак

Ове инструкције ће вам дати копију пројекта и помоћи ће вам да га покренете на локалној машини у сврси развоја и тестирања. Погледајте  [deployment](#deployment) за напомене о томе како имплементирати пројекат на живом систему.

### Предуслови

1. [Composer](https://getcomposer.org/)
1. [Docker](https://docs.docker.com/install/)

### Инсталација

Корак по корак низ примера који ће вам показати како да покренете пројекат за развој.

Прво, потребно је да креирамо пројекат и скинемо инстанцу пројекта са репозиторијума.

```
composer create-project cubes-doo/laravel-skeleton-basic newProjectName
```

Сада, ако желите да користите доцкер, прво свуците докер датотеке са нашег 'docker-compose' гит репозиторијума:

```
git clone https://github.com/cubes-doo/docker-compose.git docker
```

Следеће, треба копирати `docker/.env.example` у нову `docker/.env` датотеку

```
cp docker/.env.example docker/.env
```

Сада, нешто много узбудљивије :grin:, покренућемо наш доцкер контејнер!

```
sudo docker/docker-start.sh
```

Пошто може доћи до неслагања UID-a на различитим машинама, покренућемо:

```
cd docker ; ./docker-usermod.sh ; cd ../
```

за сваки случај. Кратко објашњење: kоришћење ове команде обезбеђује да имате исте привилегије као корисник у докер контејнеру, као што би имали на својој машини.

Сада можемо покренути:

```
sudo docker/docker-console.sh
```

како бисмо ушли у докер конзолу, и онда у њој покрећемо:

```
composer install
```

у датотеци .енв у роот-у пројекта проверити да ли постоји APP_KEY и да ли има вредност (пример: 'APP_KEY=base64:Q1teAHYgmZtZ5ZFD1CJZp8yzlQuBqUeXsz3Mn++/yl3='), ако нема покренути:

```
php artisan key:generate
```

За прављење основне стрктуре базе и пуњење тест подацима, неопходним за почетак рада, покренути:

```
php artisan migrate --seed
```

и спремни сте за почетак рада на пројекту!

Тема [theme](https://coderthemes.com/codefox/menu-dark/index.html) коришћена на овом пројекту, развијена од стране [Coderthemes](https://coderthemes.com/), није бесплатна.Тако да овај пројекат вам неће значити без плаћене лиценце. Уколико сте један од наших колага у "Cubes"-у [Cubes](https://cubes.rs/), позиционирајте се у `public/` директоријум:

```
cd public
```

и клонирајте тему са гитлаб репозиторијума:

```
git clone git@gitlab.cubes.rs:web/laravel-skeleton-theme.git theme
```

Слободно се улогујте и истражујте! По дефаулт-у пројекат је постављен на http://localhost:7737. Уколико вам то не одговара, можете променити дати урл у датотекама `docker/.env` и `.env` .

## Провера кода

Тренутнo користимо само [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer), као "composer"-ову зависност. Можете пронаћи правила у `.php_cs`. 

Постоје и две команде које олакшавају проверу кода:

```
sudo docker/docker-php-cs-check.sh
```
> ради "dry-run" "php-cs-fixer"-а, обавештава вас само шта треба бити промењено

и
```
sudo docker/docker-php-cs-fix.sh
```
> заправо исправља код помоћу "php-cs-fixer"-a

### Аутоматска провера кода

За аутоматску проверу кода,можете регистровати git "hooks" које ће покренути једну од горе наведених команди. Ми преферирамо `commit` "hook", али ви можете користити ону која вама одговара  :smile:

```
Дати пример
```

## Покретање тестова

Направили смо скрипту, која покреће тестове у оквиру докер контејнера:

```
sudo docker/docker-unit-test-run.sh
```

Ова скрипта ће изводити све изложене тестове у `tests/` директоријуму.

## Постављање

Додатне напомене о томе како имплементирати пројекат на живом систему

## Литература

 - основе
    - [Laravel](https://laravel.com/docs/5.7)
    - [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
    - [Docker](https://docs.docker.com/get-started/)
 - за оне који знају
    - [Simple Laravel TDD](https://medium.com/@jsdecena/simple-tdd-in-laravel-with-11-steps-c475f8b1b214)
    - [Easy to use app for .json based translations in Laravel](https://github.com/christofferok/langly)
    - [jQuery DataTables API for Laravel 4|5](https://github.com/yajra/laravel-datatables)
 - за храбре
    - [Sandi Metz - Code Smells & Refactoring (RailsConf2016)](https://www.youtube.com/watch?v=D4auWwMsEnY)
    - [RubyConf 2015 - How to Stop Hating your Test Suite by Justin Searls](https://www.youtube.com/watch?v=VD51AkG8EZw)
    - [Justin Searls - Breaking up (with) your test suite (Ancient City Ruby 2014)](https://www.youtube.com/watch?v=9_3RsSvgRd4)
    - [Justin Searls - The Failures of "Intro to TDD"](http://blog.testdouble.com/posts/2014-01-25-the-failures-of-intro-to-tdd)

## Направљено са

* [Composer](https://getcomposer.org/) - ПХП систем управљања пакетима
* [Laravel 5.8](https://laravel.com/docs/5.8/) - Коришћен "framework"
* [Docker](https://docs.docker.com/) - Контејнерска платформа

## Verzionisanje

Користимо [SemVer](http://semver.org/) за верзионисање. За доступне верзије, погледајте [тагове на репозиторијуму](https://github.com/cubes-doo/laravel-skeleton-basic/releases). 

## Autori

* **Aleksandar Dimic** - *Chief Science Officer* - [aleksandar-dimic](https://github.com/aleksandar-dimic)
* **Aleksa Cvijić** - *Developer* - [acvi](https://github.com/ACvijic)

Такође погледајте [листу сарадника](https://github.com/cubes-doo/laravel-skeleton-basic/graphs/contributors) који учествују на овом пројекту.
