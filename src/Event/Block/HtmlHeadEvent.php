<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\HtmlHeadEvent.
 */

namespace Drupal\pdb\Event\Block;

use Drupal\pdb\Event\Block\BaseBlockEvent;

/**
 * Wraps block html head elements for event listeners.
 */
class HtmlHeadEvent extends BaseBlockEvent {

  /**
   * @var array
   */
  protected $html_head;

  public function __construct($component, array $html_head) {
    parent::__construct($component);

    $this->html_head = $html_head;
  }

  public function getHtmlHead() {
    return $this->html_head;
  }

  public function setHtmlHead(array $html_head) {
    $this->html_head = $html_head;
  }

}
