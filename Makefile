.PHONY: install nuke status

install:
	@ddev start -y
	@ddev composer install -v

install-site:
	@if [ ! -f ./.env ]; then cp ./.env.example ./.env; fi;
	@sed -i "s/# SETTINGS_ENVIRONMENT=\"dev\"/SETTINGS_ENVIRONMENT=\"dev\"/g" ./.env;
	@sed -i "s/SETTINGS_ENVIRONMENT=\"prod\"/# SETTINGS_ENVIRONMENT=\"prod\"/g" ./.env;
	@sed -i "s/# DRUSH_OPTIONS_URI=http:\/\/example.com/DRUSH_OPTIONS_URI=https:\/\/drupal-tpl.ddev.site/g" ./.env;
	@ddev start -y
	@ddev exec drush -y si --existing-config --account-name=admin --account-pass=password
	@echo "Login with: admin / password at https://drupal-tpl.ddev.site/user/login"
	@echo "Or use this direct link:"
	@echo ddev drush uli

nuke:
	@composer nuke
	@rm -f ./.env

st: status
status:
	@ddev exec drush status;

login: uli
uli:
	@ddev exec drush uli;

qa:
	@ echo '>> Run PHPcs from config file phpcs.xml.dist...'
	@ if ddev exec vendor/bin/phpcs --colors -s -p --parallel=75 --report-full --report-summary; then exit 0; else exit 0; fi
	@ echo '>> Run PHPMD from config file phpmd.xml.dist...'
	@ if ddev exec vendor/bin/phpmd web/modules/custom/ --exclude 'tests/*,**/tests/*' text phpmd.xml.dist --ignore-errors-on-exit --ignore-violations-on-exit; then exit 0; else exit 0; fi
	@ echo '>> Run PHPStan from config file phpsta.neon.dist...'
	@ if ddev exec vendor/bin/phpstan analyse web/modules/custom; then exit 0; else exit 0; fi
