.PHONY: install nuke status

install:
	@if [ ! -f ./.env ]; then cp ./.env.example ./.env; fi;
	@sed -i "s/# SETTINGS_ENVIRONMENT=\"dev\"/SETTINGS_ENVIRONMENT=\"dev\"/g" ./.env;
	@sed -i "s/SETTINGS_ENVIRONMENT=\"prod\"/# SETTINGS_ENVIRONMENT=\"prod\"/g" ./.env;
	@sed -i "s/# DRUSH_OPTIONS_URI=http:\/\/example.com/DRUSH_OPTIONS_URI=https:\/\/drupal-tpl.ddev.site/g" ./.env;
	@ddev start -y
	@ddev exec drush -y si --existing-config --account-name=admin --account-pass=password
	@echo "Login with: admin / password at https://drupal-tpl.ddev.site/user/login"

nuke:
	@composer nuke
	@rm -f ./.env

st: status
status:
	@ddev exec drush status;

login: uli
uli:
	@ddev exec drush uli;
