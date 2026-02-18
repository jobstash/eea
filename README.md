# Enterprise ethereum alliance

## 1. Description

Dedicated wordpress theme and installation for https://entethalliance.org/

The Stack requires:

1. Docker -> https://www.docker.com/
2. Node.js -> https://nodejs.org/en
3. Composer -> https://getcomposer.org/


## 2. Local Development

<!-- How does a developer starts working on this? -->

### Getting Started

First of all you need to clone the repository locally on your machine and switch to branch `develop`, this is the branch where  all the new features will be merged into.

After cloning the repo:

1. Go to project root directory
2. Run `cp .env.sample .env && cp auth.json.sample auth.json`
3. Run `make start` to start the project.
4. Run `make composer install` to install backend dependencies
5. Wait a bit, until WP installation done and open [localhost](http://localhost) or [localhost/wp-admin](http://localhost/wp-admin) (please check for the correct http port. -- _default is 80_)
6. Go to (adminer) http://localhost/8000 to import some **[seed] (this will be provided but not in repo, because we don't want to expose data).
7. Let's now finish the setup to run the frontend!

### Run the frontend 

A step-by-step guide to start (launch) the frontend part, and start working on it.

1. Go to directory `/wp-content`
2. upload the `/uploads` folder (this will be provided separately)
3. Run `npm install` to **install front-end** dependencies
4. Run `npm dev` to **start working** on the front-end
5. Run `npm prod` to **create production assets** (CSS/JS)


### Stopping local development

1. `make stop` will take down your containers _(runs a `docker-compose stop` with the right file)_


### Troubleshooting

> ⚠️ this deletes your local DB (`down -v`), and re`build`s everything, but:

**If something doesn't work**: try running `make clean`

## 3. Deploy

### Manual

1. Run `make prod`
2. Upload the theme folder to the server

### CI/CD (GitHub Actions → WP Engine)

Theme deploys automatically when you push (develop → staging, main → production):

- **Staging:** push to branch `develop` → deploys to https://eeastage.wpenginepowered.com/
- **Production:** push to branch `main` → deploys to https://entethalliance.org/

**One-time setup:**

1. **SSH key for WP Engine SSH Gateway**  
   Generate an ED25519 key (passwordless). Add the **public** key in [WP Engine User Portal](https://my.wpengine.com) → SSH Gateway → Add key. Add the **private** key in GitHub repo → Settings → Secrets and variables → Actions → New repository secret: name `WPE_SSHG_KEY_PRIVATE`, value = private key contents.

2. **Environment names**  
   In the same GitHub Secrets, add:
   - `WPE_STAGING_ENV` = WP Engine environment name for staging (e.g. the install/env name for eeastage in the portal).
   - `WPE_PRODUCTION_ENV` = WP Engine environment name for production (e.g. the install/env name for entethalliance.org).

Workflows: [.github/workflows/deploy-staging.yml](.github/workflows/deploy-staging.yml), [.github/workflows/deploy-production.yml](.github/workflows/deploy-production.yml).  
Details: [WP Engine – GitHub Action deploy](https://wpengine.com/support/github-action-deploy/).
