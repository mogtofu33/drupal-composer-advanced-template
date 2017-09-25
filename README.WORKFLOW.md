# Workflow

## Settings
<pre>
settings.php # Should include dev or prod file depending env.
settings.dev.php
settings.prod.php
</pre>

## Dev > Prod

### On DEV

#### Daily export config (update both sync and dev_config)
<pre>
drush cr
drush cex
</pre>

### Push config changes
<pre>
# Create specific branch
git checkout -b dev-for-merge
# Commit config
git add config/
git commit -m "Config changes:..."
git push origin dev-for-merge
# Create a merge request
</pre>

#### Merge config

### On PROD

#### Pull changes
<pre>
git pull origin master
</pre>

#### Import configuration
<pre>
drush cr
drush cim
</pre>

## Prod > dev

### On Prod

#### Routine export of changes
<pre>
drush cr
drush cex
</pre>

### Git push config changes
<pre>
# Create specific branch
git checkout -b prod-sync-for-merge
# Commit config
git add config/
git commit -m "Prod config export:...."
git push origin prod-sync-for-merge
# Create a merge request
</pre>

### On dev

### Git pull
<pre>
git pull origin master
</pre>

#### Import to merge last prod changes
<pre>
drush cr
drush csim dev_config
</pre>

#### Check for config overrides from production, and get those back into code.
<pre>drush cex</pre>
