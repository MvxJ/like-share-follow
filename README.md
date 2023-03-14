# Symfony 6 and PHP 8 Application
## _Like Share Follow_

### Assumptions

- Like Share Follow is an Twitter clone app - user can create account follow people brows, comments and likes other posts.
- To create, like and comment post user should create account and activate it via link in email that he receive.
- Default user can only see posts, unless author of post select the 'extra privacy' option.
- 'Extra privacy' - this option allow autor of the post to make post hidden, only people that user follow can see this post.
- User profile is default empty user can add information to the profile and upload user profile photo.

## Technologies

- [Tailwind CSS]
- [PHP 8.1]
- [Docker]
- [Twig]
- [Mysql]

## Installation

This app is easy to set-up localy it requaires only the Docker
To install app you need to clone it to your local machine and inside the project folder run following commands:

Build docker image:
```sh
docker compose up -d
```

After docker will build succesfully you need to enter the application:

```sh
docker exec -it symfony_app bash
```

Insite app directory you need to create database schema and load some data fixtures using following commands:

```sh
php bin/console doctrine:schema:create
```

```sh
php bin/console doctrine:fixtures:load
```

## Development

Finally when app is launched you can visit the ```localhost:8000``` and play around
App shares additional tools to menage database and catch send emails:

| Service | Web Address    | Creditentials                                                     |
|---------|----------------|-------------------------------------------------------------------|
| Mailer  | localhost:8000 | none                                                              |
| Adminer | localhost:8001 | server - database, user - app, password - app, database - symfony |
