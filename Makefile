run:
	clear && symfony server:start --port=8004

seed:
	clear && php bin/console app:seed:dev

db-reset: db-seed
