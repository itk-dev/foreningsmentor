<?php

namespace Drupal\foreningsmentor\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ThemeNegotiator.
 *
 * Controls which routes the foreningsmentor_theme applies to.
 *
 * @package Drupal\foreningsmentor\Theme
 */
class ForeningsmentorThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Renegotiate paths from administration theme to foreningsmentor_intra theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route service.
   *
   * @return bool
   *   Whether to apply foreningsmentor theme to routes.
   */
  public function applies(RouteMatchInterface $route) {
    $node = $route->getParameter('node');

    if ($route->getRouteName() === 'foreningsmentor.sign_up_form') {
      return TRUE;
    }

    if (isset($node) &&
        in_array($node->getType(), ['page']) &&
        $route->getRouteName() != 'entity.node.edit_form') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route) {
    return 'foreningsmentor_theme';
  }

}
