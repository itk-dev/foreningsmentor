<?php

$config_directories['sync'] = '../config/sync';

$settings['hash_salt'] = '123';

$databases['default']['default'] = array (
  'database' => 'db',
  'username' => 'db',
  'password' => 'db',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['trusted_host_patterns'] = [
  '^foreningsmentor\.local\.itkdev\.dk$',
];
