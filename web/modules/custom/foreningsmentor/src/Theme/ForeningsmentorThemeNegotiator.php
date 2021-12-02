<?php

/**
 * Theme negotiator for foreningsmentor_theme.
 */

namespace Drupal\foreningsmentor\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ThemeNegotiator
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
   * @return bool
   */
  public function applies(RouteMatchInterface $route) {
    $node = $route->getParameter('node');

    if ($route->getRouteName() === 'foreningsmentor.sign_up_form') {
      return true;
    }

    if (isset($node) &&
        in_array($node->getType(), ['page']) &&
        $route->getRouteName() != 'entity.node.edit_form') {
      return true;
    }

    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route) {
    return 'foreningsmentor_theme';
  }
}
