-include .env

export
XDEBUG_MODE=off
#MAKEFLAGS += --no-print-directory

ifneq ($(origin NAME),command line)
NAME := app
endif

DOCKER_EXEC := docker-compose exec -u 1000:1000 -w /var/www/source -it $(NAME)

# ========================================
# common
# ----------------------------------------
define print_headline
	@echo "----------------------------------------------------------------"
	@echo ">>> 実行中: [ $(1) ]"
	@echo "----------------------------------------------------------------"
endef

# ========================================
# docker
# ----------------------------------------
.PHONY: docker-env
docker-env:
	@echo "COMPOSE_PROJECT_NAME  : $(COMPOSE_PROJECT_NAME)"
	@echo "__WSL_COMPOSE_PROJECT_DIR : $(__WSL_COMPOSE_PROJECT_DIR)"

.PHONY: docker-up
docker-up:
	$(call print_headline,${@})
	@docker compose -f docker-compose.yml up -d

.PHONY: docker-down
docker-down:
	$(call print_headline,${@})
	@docker compose -f docker-compose.yml down

.PHONY: docker-login
docker-login:
	$(call print_headline,${@})
	@docker-compose exec $(NAME) bash --login

SERVICES ?= mysql_master mysql_slave
.PHONY: docker-reset
docker-reset:
	@docker-compose -f docker-compose.yml down -v $(SERVICES)
	@docker-compose -f docker-compose.yml up -d --build $(SERVICES)

# ========================================
# app
# ----------------------------------------
.PHONY: app-init
app-init:
	$(call print_headline,${@})
	@$(MAKE) app-env-setup
	@$(MAKE) composer install
	@$(MAKE) app-reset
	@$(MAKE) app-clean

.PHONY: app-env-setup
app-env-setup:
	$(call print_headline,${@})
	@if [ ! -f .env ]; then \
		cp .env.local .env; \
		echo ".env.local から .env を作成しました"; \
	else \
		echo ".env は既に存在するためスキップしました"; \
	fi

.PHONY: app-clean
app-clean:
	$(call print_headline,${@})
	chown -R 1000:1000 storage bootstrap/cache
	chmod -R 755 storage bootstrap/cache
	rm -rf storage/framework/cache/*

	@$(MAKE) artisan config:clear
	@$(MAKE) artisan cache:clear
	@$(MAKE) artisan view:clear
	@$(MAKE) artisan optimize:clear
	@$(MAKE) composer dump-autoload
	@$(MAKE) job-restart

.PHONY: app-reset
app-reset:
	$(call print_headline,${@})
	@$(MAKE) migrate-fresh-all
	@$(MAKE) migrate-all
	@$(MAKE) migrate-seeder-dev

# ========================================
# composer
# ----------------------------------------
COMPOSER_ARGS := $(filter-out composer,$(MAKECMDGOALS))

.PHONY: composer
composer:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) composer $(COMPOSER_ARGS)

# ========================================
# artisan
# ----------------------------------------
ARTISAN_ARGS := $(filter-out artisan,$(MAKECMDGOALS))

.PHONY: artisan
artisan:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) php artisan $(ARTISAN_ARGS)

# ========================================
# php-cs-fixer
# ----------------------------------------
.PHONY: php-fix-dry
php-fix-dry:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: php-fix
php-fix:
	@read -p "commit? [y/N]: " ans && [ "$$ans" = "y" ]
	@$(DOCKER_EXEC) vendor/bin/php-cs-fixer fix --diff

# ========================================
# php-stan
# ----------------------------------------
.PHONY: php-stan
php-stan:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) vendor/bin/phpstan analyse

# ========================================
# openapi
# ----------------------------------------
.PHONY: openapi-build
openapi-build:
	cd ./docs/apidog && sh build.sh

# ========================================
# migrate
# ----------------------------------------
.PHONY: migrate-all
migrate-all:
	$(call print_headline,${@})
	@$(MAKE) migrate-app
	@$(MAKE) migrate-log
	@$(MAKE) migrate-test

.PHONY: migrate-app
migrate-app:
	$(call print_headline,${@})
	@$(MAKE) migrate DB_NAME=app_db DB_PATH=database/migrations

.PHONY: migrate-test
migrate-test:
	$(call print_headline,${@})
	@$(MAKE) migrate DB_NAME=test_db DB_PATH=database/migrations

.PHONY: migrate-log
migrate-log:
	$(call print_headline,${@})
	@$(MAKE) migrate DB_NAME=log_db	DB_PATH=database/migrations/log

.PHONY: migrate
migrate:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) env DB_DATABASE=$(DB_NAME) php artisan migrate --path=$(DB_PATH)

# ========================================
# seeder
# ----------------------------------------
.PHONY: migrate-seeder-dev
migrate-seeder-dev:
	$(call print_headline,${@})
	@$(MAKE) migrate-seeder DB_NAME=app_db SD_NAME=DevMaster
	@$(MAKE) migrate-seeder DB_NAME=app_db SD_NAME=DevUser
	@$(MAKE) migrate-seeder DB_NAME=log_db SD_NAME=DevLog

.PHONY: migrate-seeder
migrate-seeder:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) env DB_DATABASE=$(DB_NAME) php artisan db:seed $(SD_NAME)

# ========================================
# migrate:reset
# ----------------------------------------
.PHONY: migrate-reset-all
migrate-reset-all:
	$(call print_headline,${@})
	@$(MAKE) migrate-reset-app
	@$(MAKE) migrate-reset-log
	@$(MAKE) migrate-reset-test

.PHONY: migrate-reset-app
migrate-reset-app:
	$(call print_headline,${@})
	@$(MAKE) migrate-reset DB_NAME=app_db DB_PATH=database/migrations

.PHONY: migrate-reset-test
migrate-reset-test:
	$(call print_headline,${@})
	@$(MAKE) migrate-reset DB_NAME=test_db DB_PATH=database/migrations

.PHONY: migrate-reset-log
migrate-reset-log:
	$(call print_headline,${@})
	@$(MAKE) migrate-reset DB_NAME=log_db DB_PATH=database/migrations/log

.PHONY: migrate-reset
migrate-reset:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) env DB_DATABASE=$(DB_NAME) php artisan migrate:reset --path=$(DB_PATH)

# ========================================
# migrate-fresh
# ----------------------------------------
.PHONY: migrate-fresh-all
migrate-fresh-all:
	$(call print_headline,${@})
	@$(MAKE) migrate-fresh app_db
	@$(MAKE) migrate-fresh test_db
	@$(MAKE) migrate-fresh log_db

DB_NAME_ARGS := $(filter-out migrate-fresh,$(MAKECMDGOALS))

.PHONY: migrate-fresh
migrate-fresh:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) env DB_DATABASE=$(DB_NAME_ARGS) php artisan migrate:fresh

# ========================================
# job
# ----------------------------------------
# @note 【開発用】Job変更時にソースコードを自動で再読み込みする（queue:restart不要）
.PHONY: job-restart-dev
job-restart-dev:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) php artisan queue:listen

.PHONY: job-restart
job-restart:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) php artisan queue:restart

.PHONY: job-work
job-work:
	$(call print_headline,${@})
	@$(DOCKER_EXEC) php artisan queue:work

.DEFAULT:
	@:
