<?php

namespace Drupal\foreningsmentor\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class CopyToClipboardCommand implements CommandInterface {
  protected $list;

  /**
   * CopyToClipboardCommand constructor.
   */
  public function __construct($list) {
    $this->list = $list;
  }

  public function render() {
    return [
      'command' => 'copyToClipboardCommand',
      'list' => $this->list,
    ];
  }

}
