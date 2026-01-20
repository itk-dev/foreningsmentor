<?php

namespace Drupal\foreningsmentor_fixtures\Helper;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileRepository;
use Drupal\Core\File\FileSystem;
use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Helper class for fixtures.
 */
class Helper {

  /**
   * The ExtensionPathResolver service.
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected ExtensionPathResolver $pathResolver;

  /**
   * The FileRepository service.
   *
   * @var \Drupal\file\FileRepository
   */
  protected FileRepository $fileRepo;

  /**
   * The FileSystem service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected FileSystem $fileSystem;

  /**
   * Constructor.
   */
  public function __construct(ExtensionPathResolver $pathResolver, FileRepository $fileRepo, FileSystem $fileSystem) {
    $this->pathResolver = $pathResolver;
    $this->fileRepo = $fileRepo;
    $this->fileSystem = $fileSystem;
  }

  /**
   * Create image entities from folder files.
   */
  public function createImagesFromAssets(): array {
    $images = [];
    $image_source_path = $this->pathResolver->getPath('module', 'foreningsmentor_fixtures') . '/assets/images';
    $image_target_path = 'public://fixtures/assets/images';
    $this->fileSystem->prepareDirectory($image_target_path, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

    // Loop over .jpg images to add them properly to the file system.
    foreach (glob($image_source_path . '/*.{jpg}', GLOB_BRACE) as $image) {
      $destination = $this->fileSystem->copy($image, $image_target_path . '/' . basename($image), FileExists::Replace);
      $images[] = $destination;
    }

    return $images;
  }

  /**
   * @throws EntityStorageException
   */
  public function createIntranetMenu()
  {
    // Can't be done in a single loop because the parents are not created yet.
    $items = [
      'base_items' => [
        'venteliste' => 'Venteliste',
        'forlob' => 'Forløb',
        'frivillige' => 'Frivillige',
        'born' => 'Børn',
        'foraeldre' => 'Forældre',
        'foreninger' => 'Foreninger',
        'mentor/forlob' => 'Mentorforløb',
        'user' => 'Profil',
        'user/logout' => 'Log ud',
        'admin/content' => 'Admin'
      ],
      'admin_sub_items' => [
        'admin/content' => 'Indhold',
        'aktiviteter' => 'Aktivitetsforløb',
        'admin/people' => 'Brugere',
        'admin/config/regional/translate' => 'Oversættelser',
        'admin/structure/taxonomy' => 'Taksonomier',
        'admin/site-setup/general' => 'Sideindstillinger',
        'admin/structure/menu/manage/main' => 'Menu'
      ],
      'page_settings_items' => [
        'signup' => 'Tilmelding'
      ]

    ];

    $menuLinkStorage = $this->entityTypeManager->getStorage('menu_link_content');
    $intranetMenuIds = $menuLinkStorage->getQuery()->accessCheck()->condition('menu_name', 'intranet')->execute();
    $menuLinks = $menuLinkStorage->loadMultiple($intranetMenuIds);
    $menuLabels = array_map(fn($link) => $link->label(), $menuLinks);

    // Create base menu items.
    foreach ($items['base_items'] as $path => $title) {
      // Don't do anything if the menu item already exists.
      if (in_array($title, $menuLabels)) {
        break;
      }

      $menuItem = [
        'title' => $title,
        'link' => ['uri' => 'internal:/' . $path],
        'menu_name' => 'intranet',
        'expanded' => TRUE,
      ];

      $menu_link = MenuLinkContent::create($menuItem);
      $menu_link->save();
    }

    // Determine admin menu parent.
    $adminParentFetched = $menuLinkStorage->getQuery()
      ->accessCheck()
      ->condition('menu_name', 'intranet')
      ->condition('link__uri', 'internal:/admin/content')
      ->execute();

    $adminParent = $menuLinkStorage->load(array_pop($adminParentFetched));

    // Create admin menu items.
    foreach ($items['admin_sub_items'] as $path => $title) {
      // Don't do anything if the menu item already exists.
      if (in_array($title, $menuLabels)) {
        break;
      }

      $menuItem = [
        'title' => $title,
        'link' => ['uri' => 'internal:/' . $path],
        'menu_name' => 'intranet',
        'expanded' => TRUE,
        'parent' => 'menu_link_content:' . $adminParent->uuid()
      ];

      $menu_link = MenuLinkContent::create($menuItem);
      $menu_link->save();
    }

    // Determine page settings parent.
    $pageSettingsParentFetched = $menuLinkStorage->getQuery()
      ->accessCheck()
      ->condition('menu_name', 'intranet')
      ->condition('link__uri', 'internal:/admin/site-setup/general')
      ->execute();
    $pageSettingsParent = $menuLinkStorage->load(array_pop($pageSettingsParentFetched));

    // Create page settings menu items.
    foreach ($items['page_settings_items'] as $path => $title) {
      // Don't do anything if the menu item already exists.
      if (in_array($title, $menuLabels)) {
        break;
      }

      $menuItem = [
        'title' => $title,
        'link' => ['uri' => 'internal:/' . $path],
        'menu_name' => 'intranet',
        'expanded' => TRUE,
        'parent' => 'menu_link_content:' . $pageSettingsParent->uuid()
      ];

      $menu_link = MenuLinkContent::create($menuItem);
      $menu_link->save();
    }
  }
}
