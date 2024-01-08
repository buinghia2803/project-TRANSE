# DOCKER FOR BASE ADMINLTE

## Setup

**Setup Docker**

1. Create `.env` from `.env.example`.
- Change Network, Container Schema
- Change port for web, database and mail hog
2. Clone Base Adminlte: `git clone https://gitlab.com/relipa/d1/d1nuxtjs/D1LaravelBase-adminlte.git`
3. In root folder run `docker-compose up -d` (if first build then add --build)
4. Run `docker exec -it {container_workspace_name} bash` to access into workspace.
5. In workspace container:
- Create `.env` from `.env.example`. Set config for DB, mail-hog, ...
- Run: `composer install`
- Run: `php artisan key:generate`
- Run: `php artisan migrate --seed`
- Run: `npm run dev`
6. In Browser: `http://localhost:{APP_PORT}`

**Stop Docker**
- Stop container: `docker-compose down`
- Stop all container and remove volumes: `docker-compose down -v`