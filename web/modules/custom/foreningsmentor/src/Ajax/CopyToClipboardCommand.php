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
    $result = '';

    $last_key = array_search(end($this->list), $this->list);
    foreach ($this->list as $key => $object) {
      $result = $result . '"' . $object . '"' . '<' . $key . '>';
      if ($key != $last_key) {
        $result .= '; ';
      }
    }

    return [
      'command' => 'copyToClipboardCommand',
      'list' => $result,
    ];
  }

}
