# Enterprise Ethereum Alliance

## 1. Description

WordPress theme and setup for **https://entethalliance.org/**.

**Requirements:** Docker, Node.js, Composer.

- Docker: https://www.docker.com/
- Node.js: https://nodejs.org/
- Composer: https://getcomposer.org/

---

## 2. Environments (Staging & Production)

There are **two remote environments**, both on WP Engine:

| Environment | URL | How to deploy | Notes |
|-------------|-----|----------------|-------|
| **Staging** | https://eeastage.wpenginepowered.com/ | Push to branch **`develop`** | Protected by Basic Auth (see below). Use for testing before production. |
| **Production** | https://entethalliance.org/ | Push to branch **`main`** | Live site. |

- **Deploy to Staging:** push (or merge) to `develop` → GitHub Actions builds the theme (including frontend assets) and deploys to staging.
- **Deploy to Production:** merge `develop` into `main` and push → GitHub Actions builds and deploys to production.

Staging is behind HTTP Basic Auth. Credentials:

- **User:** `eeastage`
- **Password:** `ethereum84874`

---

## 3. Local Development

### Getting started

1. Clone the repo and switch to branch **`develop`** (where features are merged).
2. From the **project root**:
   ```bash
   cp .env.sample .env && cp auth.json.sample auth.json
   make start
   make composer install
   ```
3. Wait until WordPress is up, then open:
   - **Site:** http://localhost:8080  
   - **Admin:** http://localhost:8080/wp-admin  
   - **phpMyAdmin (DB/seed):** http://localhost:8000  

   The stack is **trafex/wordpress** (PHP-FPM + Nginx) on port **8080**.

### Frontend (theme)

From the **project root**:

1. **Install dependencies and run dev server** (with hot reload):
   ```bash
   cd wp-content/themes/wp-starter
   npm install
   npm run dev
   ```
   Keep this terminal running while you work on CSS/JS.

2. **Production build (e.g. to test built assets locally):**
   ```bash
   cd wp-content/themes/wp-starter
   npm run build
   ```
   On staging/production the theme is built automatically in CI before deploy; you don’t need to commit `dist/`.

### Stopping

- **Stop containers:** `make stop`

### Troubleshooting

If the stack misbehaves, a full reset (removes local DB and rebuilds) is:

```bash
make clean
```

---

## 4. Deploy (summary)

### CI/CD (recommended)

- **Staging:** push to **`develop`** → workflow builds theme + frontend and deploys to **https://eeastage.wpenginepowered.com/**
- **Production:** push to **`main`** → workflow builds and deploys to **https://entethalliance.org/**, and creates git tag **vX.Y.Z** from `VERSION` (if not already present).

### Release flow (one version to update)

1. Create branch **`release/x.x.x`** (e.g. `release/1.1.0`).
2. Update **only** the root file **`VERSION`** with the new version (e.g. `1.1.0`). Update [CHANGELOG.md](CHANGELOG.md) (add `[x.x.x] - date` and move “Unreleased” notes into it).
3. Merge into **`main`** (e.g. via PR). On push to `main`, the workflow builds the theme (injecting `VERSION` into `style.css` and `package.json`), deploys to production, and creates the tag **vx.x.x** automatically.

You never edit `style.css` or `package.json` for the version; CI keeps them in sync from `VERSION`.

Workflows: [deploy-staging.yml](.github/workflows/deploy-staging.yml), [deploy-production.yml](.github/workflows/deploy-production.yml).  
WP Engine docs: [GitHub Action deploy](https://wpengine.com/support/github-action-deploy/).

### One-time GitHub setup

1. **SSH key for WP Engine**  
   Create an ED25519 key. Add the **public** key in [WP Engine User Portal](https://my.wpengine.com) → SSH Gateway. Add the **private** key in the repo: Settings → Secrets and variables → Actions → secret `WPE_SSHG_KEY_PRIVATE`.

2. **Secrets**  
   In the same place, add:
   - `WPE_STAGING_ENV` = WP Engine staging environment name (e.g. for eeastage).
   - `WPE_PRODUCTION_ENV` = WP Engine production environment name (e.g. for entethalliance.org).

