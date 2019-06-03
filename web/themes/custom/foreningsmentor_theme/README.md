# Foreningsmentor theme

## Theme development

### Install dependencies for development

Run this in the root of the theme directory

```sh
yarn install
```

### Build files

#### Development

Build once

```sh
yarn dev
```

Build when files changes

```sh
yarn watch
```

#### Production

Build production ready assets

```sh
yarn build
```

### Disable cache for local development

We use drupal console to set the site in development mode. This will disable template cache.

From project root (htdocs) run this

```sh
./vendor/drupal/console/bin/drupal site:mode dev
```
