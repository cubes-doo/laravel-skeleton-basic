# Laravel Skeleton (basic)

<p align="center">
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Version">
      <img src="https://img.shields.io/packagist/v/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
    <a href="https://packagist.org/packages/cubes-doo/laravel-skeleton-basic" alt="Downloads">
      <img src="https://img.shields.io/packagist/dm/cubes-doo/laravel-skeleton-basic.svg" />
   </a>
</p>

Laravel starter projekat, za definisanje standarda i brzinu pokretanja :rocket:

*Pročitajte ovo na drugim jezicima: [English](README.md), [Српски](README.sr-cyr.md)*

## Početak

Ove instrukcije će vam dati kopiju projekta i pomoći će vam da ga pokrenete na lokalnoj mašini u svrsi razvoja i testiranja. Pogledajte [deployment](#deployment) za napomene o tome kako implementirati projekat na živom sistemu.

### Preduslovi

1. [Composer](https://getcomposer.org/)
1. [Docker](https://docs.docker.com/install/)

### Instalacija

Korak po korak niz primera koji će vam pokazati kako da pokrenete projekat za razvoj.

Prvo, potrebno je da kreiramo projekat i skinemo instancu projekta sa repozitorijuma.

```
composer create-project cubes-doo/laravel-skeleton-basic newProjectName
```

Sada, ako želite da koristite docker, prvo svucite docker datoteke sa našeg 'docker-compose' git repozitorijuma:

```
git clone https://github.com/cubes-doo/docker-compose.git docker
```

Sledeće, treba kopirati `docker/.env.example` u novu `docker/.env` datoteku

```
cp docker/.env.example docker/.env
```

Sada, nešto mnogo uzbudljivije :grin:, pokrenućemo naš docker kontejner!

```
sudo docker/docker-start.sh
```

Pošto može doći do neslaganja UID-a na različitim mašinama, pokrenućemo:

```
cd docker ; ./docker-usermod.sh ; cd ../
```

za svaki slučaj. Kratko objašnjenje: korišćenje ove komande obezbeđuje da imate iste privilegije kao korisnik u docker kontejneru, kao što bi imali na svojoj mašini.

Sada možemo pokrenuti:

```
sudo docker/docker-console.sh
```

kako bismo ušli u docker konzolu, i onda u njoj pokrećemo:

```
composer install
```

u datoteci .env u root-u projekta proveriti da li postoji APP_KEY i da li ima vrednost (primer: 'APP_KEY=base64:Q1teAHYgmZtZ5ZFD1CJZp8yzlQuBqUeXsz3Mn++/yl3='), ako nema pokrenuti:

```
php artisan key:generate
```

Za pravljenje osnovne strkture baze i punjenje test podacima, neophodnim za početak rada, pokrenuti:

```
php artisan migrate --seed
```

i spremni ste za početak rada na projektu!

Tema [theme](https://coderthemes.com/codefox/menu-dark/index.html) korišćena na ovom projektu, razvijena od strane [Coderthemes](https://coderthemes.com/), nije besplatna.Tako da ovaj projekat vam neće značiti bez plaćene licence. Ukoliko ste jedan od naših kolaga u Cubes-u [Cubes](https://cubes.rs/), pozicionirajte se u `public/` direktorijum:

```
cd public
```

i klonirajte temu sa gitlab repozitorijuma:

```
git clone git@gitlab.cubes.rs:web/laravel-skeleton-theme.git theme
```

Slobodno se ulogujte i istražujte! Po default-u projekat je postavljen na http://localhost:7737. Ukoliko vam to ne odgovara, možete promeniti dati url u datotekama `docker/.env` i `.env` .

## Provera koda

Trenutno koristimo samo [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer), kao composer-ovu zavisnost. Možete pronaći pravila u `.php_cs`. 

Postoje i dve komande koje olakšavaju proveru koda:

```
sudo docker/docker-php-cs-check.sh
```
> radi "dry-run" php-cs-fixer-a, obaveštava vas samo šta treba biti promenjeno

i
```
sudo docker/docker-php-cs-fix.sh
```
> zapravo ispravlja kod pomoću php-cs-fixer-a

## Pokretanje testova

Napravili smo skriptu, koja pokreće testove u okviru docker kontejnera:

```
sudo docker/docker-unit-test-run.sh
```

Ova skripta će izvoditi sve izložene testove u `tests/` direktorijumu.

## Postavljanje

Dodatne napomene o tome kako implementirati projekat na živom sistemu

## Literatura

 - osnove
    - [Laravel](https://laravel.com/docs/5.7)
    - [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
    - [Docker](https://docs.docker.com/get-started/)
 - za one koji znaju
    - [Simple Laravel TDD](https://medium.com/@jsdecena/simple-tdd-in-laravel-with-11-steps-c475f8b1b214)
    - [Easy to use app for .json based translations in Laravel](https://github.com/christofferok/langly)
    - [jQuery DataTables API for Laravel 4|5](https://github.com/yajra/laravel-datatables)
 - za hrabre
    - [Sandi Metz - Code Smells & Refactoring (RailsConf2016)](https://www.youtube.com/watch?v=D4auWwMsEnY)
    - [RubyConf 2015 - How to Stop Hating your Test Suite by Justin Searls](https://www.youtube.com/watch?v=VD51AkG8EZw)
    - [Justin Searls - Breaking up (with) your test suite (Ancient City Ruby 2014)](https://www.youtube.com/watch?v=9_3RsSvgRd4)
    - [Justin Searls - The Failures of "Intro to TDD"](http://blog.testdouble.com/posts/2014-01-25-the-failures-of-intro-to-tdd)

## Napravljeno sa

* [Composer](https://getcomposer.org/) - PHP sistem upravljanja paketima
* [Laravel 5.8](https://laravel.com/docs/5.8/) - Korišćen "framework"
* [Docker](https://docs.docker.com/) - Kontejnerska platforma

## Verzionisanje

Koristimo [SemVer](http://semver.org/) za verzionisanje. Za dostupne verzije, pogledajte [tagove na repozitorijumu](https://github.com/cubes-doo/laravel-skeleton-basic/releases). 

## Autori

* **Aleksandar Dimic** - *Chief Science Officer* - [aleksandar-dimic](https://github.com/aleksandar-dimic)
* **Aleksa Cvijić** - *Developer* - [acvi](https://github.com/ACvijic)

Takođe pogledajte [listu saradnika](https://github.com/cubes-doo/laravel-skeleton-basic/graphs/contributors) koji učestvuju na ovom projektu.
