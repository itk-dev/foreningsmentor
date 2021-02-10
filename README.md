# Foreningsmentor

## Setup for development

Create settings.local.php
```
cp web/sites/default/docker.settings.local.php web/sites/default/settings.local.php
```

Start docker containers
```
itkdev-docker-compose up -d
```

Install composer packages
```
itkdev-docker-compose composer install
```

Install drupal
```
itkdev-docker-compose drush --yes site-install --config-dir='../config/sync'
```
