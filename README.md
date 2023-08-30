# Setup

## Local setup.

1) Copy development services definitions
```
cp web/sites/development.services.yml web/sites/default/services.local.yml
```

2) In (sites/default/settings.local.php) create file if it doesn't exist. Then copy the following over:
```php
<?php

/**
 * Database connection.
 */
$databases['default']['default'] = [
 'database' => getenv('DATABASE_DATABASE') ?: 'db',
 'username' => getenv('DATABASE_USERNAME') ?: 'db',
 'password' => getenv('DATABASE_PASSWORD') ?: 'db',
 'host' => getenv('DATABASE_HOST') ?: 'mariadb',
 'port' => getenv('DATABASE_PORT') ?: '',
 'driver' => getenv('DATABASE_DRIVER') ?: 'mysql',
 'prefix' => '',
];

$settings['config_sync_directory'] = '../config/sync';

$settings['hash_salt'] = 'HASH_SALT';

$settings['trusted_host_patterns'] = [
  '^foreningsmentor\.local\.itkdev\.dk$'
];

/**
 * Add development service settings.
 */
if (file_exists(__DIR__ . '/services.local.yml')) {
  $settings['container_yamls'][] = __DIR__ . '/services.local.yml';
}

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Logging.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Set default transport to smtp to make mailhog happy.
 */
$config['symfony_mailer.settings']['default_transport'] = 'smtp';
```

3) Start docker comtainers, and install composer.
```
docker compose up -d
docker compose exec phpfpm composer install
```

... if error network frontend declared as external, but could not be found = run the following, and then rerun step 3 :
```
 docker network create frontend
```

4) Install drupal and set user admin password to test.
```
docker compose exec phpfpm /app/vendor/bin/drush --yes site-install --existing-config
docker compose exec phpfpm /app/vendor/bin/drush upwd admin test
```


5) Create example content through fixtures:
```
docker compose exec phpfpm /app/vendor/bin/drush content-fixtures:load -y
```
