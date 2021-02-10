# Foreningsmentor

## Setup for development

Start docker containers
```
docker-compose up --detach
```

Install composer packages
```
docker-compose exec phpfpm composer install
```

Install drupal
```
docker-compose exec phpfpm /app/vendor/bin/drush --yes site-install --config-dir='../config/sync'
docker-compose exec phpfpm /app/vendor/bin/drush --yes config-import
```

### Docker dev setup
Set local docker development settings for Drupal by copying the example file
```
cp web/sites/default/example.docker.settings.local.php web/sites/default/docker.settings.local.php
```

## Drupal setup
Please note custom patch to fix issue with views_bulk_operations pager (FOR-205):
```
patch/views_bulk_operations_patch_1.patch
```
