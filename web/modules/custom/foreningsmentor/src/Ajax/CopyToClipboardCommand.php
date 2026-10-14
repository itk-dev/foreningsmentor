<?php

namespace Drupal\foreningsmentor\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class for copying to clip board.
 */
class CopyToClipboardCommand implements CommandInterface {

  /**
   * The list.
   *
   * @var array
   */
  protected array $list;

  /**
   * CopyToClipboardCommand constructor.
   *
   * @param array $list
   *   The list.
   */
  public function __construct(array $list) {
    $this->list = $list;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'copyToClipboardCommand',
      'list' => $this->list,
    ];
  }

}
