#!make
include .env
$(eval export $(shell sed -ne 's/ *#.*$$//; /./ s/=.*$$// p' .env))

DOCKER_COMPOSE_FILE?=${DOCKER_COMPOSE_FILE}
CONTAINER?=${PROJECT_NAME}-wordpress-1
BUILDCHAIN?=${PROJECT_NAME}-node-1
COMPOSE=UID=${UID} GID=${GID} APP_UID=${APP_UID} docker compose -p ${PROJECT_NAME} -f $(DOCKER_COMPOSE_FILE)

.PHONY: default clean composer composer-install npm npm-install npm-dev craft restoredb bash update-clean stop start restart logs

default: build composer-install npm-install npm-dev

clean:
	${COMPOSE} down -v
	${COMPOSE} up --build --remove-orphans -d
	${COMPOSE} logs -f -t

composer:
	docker exec -it ${CONTAINER} composer \
		$(filter-out $@,$(MAKECMDGOALS))

composer-install:
	${COMPOSE} run composer bash -c 'composer install'
	
composer-update:
	${COMPOSE} run composer bash -c 'composer update'

npm:
	docker exec -it ${BUILDCHAIN} npm \
		$(filter-out $@,$(MAKECMDGOALS))

npm-install:
	${COMPOSE} run node bash -c 'npm install'
	
npm-dev:
	${COMPOSE} run node bash -c 'npm run dev'

npm-build:
	${COMPOSE} run node bash -c 'npm run build'

prod:
	${COMPOSE} run node bash -c 'npm run build'
	rm -rf theme
	mkdir theme
	cp -a wp-content/themes/wp-starter/. theme
	rm -rf theme/node_modules
	rm -rf theme/assets/js
	rm -rf theme/assets/css
	rm -f theme/vite.config.js
	rm -f theme/tailwind.config.js
	rm -f theme/postcss.config.js
	rm -f theme/main.js


upload:
	rsync -avz -e "ssh -p ${SERVER_PORT}" theme/ ${SEVER_USER}@${SERVER_NAME}:${REMOTE_PATH}/wp-content/themes/wp-starter
	rsync -avz -e "ssh -p ${SERVER_PORT}" vendor/ ${SEVER_USER}@${SERVER_NAME}:${REMOTE_PATH}/vendor

# Create an alias of prod
theme-build: prod

dev:
	${COMPOSE} run node bash -c 'npm run dev'

node-version:
	docker exec -it ${BUILDCHAIN} node -v && npm -v

bash:
	docker exec -it ${CONTAINER} /bin/sh

update-clean:
	${COMPOSE} stop
	rm -f composer.lock
	rm -rf vendor/
	rm -f package-lock.json
	rm -rf node_modules/
	${COMPOSE} start

stop:
	if [ "$$(docker ps -q -f name=${CONTAINER})" ]; then \
			${COMPOSE} stop; \
	fi

start:
	if [ ! "$$(docker ps -q -f name=${CONTAINER})" ]; then \
			${COMPOSE} up -d --remove-orphans; \
	fi

build:
	if [ ! "$$(docker ps -q -f name=${CONTAINER})" ]; then \
			${COMPOSE} up --remove-orphans --build; \
	fi

restart: stop start

logs:
	${COMPOSE} logs -f -t
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
