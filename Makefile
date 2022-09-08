up:
	clear
	make stop
	docker-compose up -d
stop:
	docker-compose stop
	docker system prune -f
full-restart:
	clear
	make stop
	docker system prune -f
	docker-compose up -d --build
shell:
	clear
	docker-compose exec $(arg) bash
build:
	docker-compose build
node-watch:
	clear
	docker-compose exec node yarn watch
node-build:
	clear
	docker-compose exec node yarn build
messenger-mail:
	clear
	docker-compose exec www php bin/console messenger:consume async -vv
fixtures:
	docker-compose exec -u 1000 www php bin/console d:f:l --no-interaction
migration:
	clear
	docker-compose exec -u 1000 www php bin/console m:migration
	docker-compose exec -u 1000 www php bin/console d:m:m --no-interaction
	make fixtures
cache-clear:
	clear
	docker-compose exec -u 1000 www php bin/console c:c
	docker-compose exec -u 1000 node npm run build
node-dev:
	clear
	docker-compose exec -u 1000 node npm run dev
logs:
	docker-compose exec www symfony serve:log

full-deploy:
	docker-compose exec www composer dump-env dev
	docker-compose exec www php bin/console d:s:d --force
	docker-compose exec www php bin/console d:s:c
	docker-compose exec www php bin/console d:f:l --no-interaction
	docker-compose exec www composer dump-env prod
	docker-compose exec www php bin/console c:c
	docker-compose exec -u 1000 node npm run build
	make stop
	docker system prune -f
	docker-compose up -d

#Ajuster les permissions
permissions:
	sudo chown -R $$USER:$$USER .
	sudo chmod -R 777 .