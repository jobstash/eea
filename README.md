# Enterprise ethereum alliance

## 1. Description

The aim of this project is to centralize a main scaffolding to be used as starter project for derivated projects. The stack is based on Worpress and to set up a new project you can use this as from Gitlab Repo to start up.

## 2. Local Development

<!-- How does a developer starts working on this? -->

### Getting Started

After cloning its repository:

1. Go to project root directory
2. Run `cp .env.sample .env && cp auth.json.sample auth.json`
3. Edit `.env` file accordingly
4. Edit auth.json file, add your personal gitlab token with at least the scope `api` (https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html#create-a-personal-access-token)
5. Run `make composer-update` OR simply `composer update`, if you have composer installed
6. Run `make build` if it's the first time, or `make start` to start the project.
7. Wait a bit, until WP installation done and open [localhost](http://localhost) or [localhost/wp-admin](http://localhost/wp-admin) (please check for the correct http port. -- _default is 80_)

### Step by step

A step-by-step guide to start (launch) the project, and start working on it.

EXAMPLE:

1. `make start` should get you http://localhost _(runs a `docker-compose up` with the right file)_
2. Run `make composer-update` to **install back-end** dependencies
3. Go to (adminer) http://localhost/db to import some **[seed](config/seed.sql.gz) data**
4. Run `npm install` to **install front-end** dependencies
5. Run `npm dev` to **start working** on the front-end
6. Run `npm prod` to **create production assets** (CSS/JS)
7. `make stop` will take down your containers _(runs a `docker-compose stop` with the right file)_

### Troubleshooting

> ⚠️ this deletes your local DB (`down -v`), and re`build`s everything, but:

**If something doesn't work**: try running `make clean`

## 3. Deploy

1. Run `make prod`
2. Upload the theme folder to the server
