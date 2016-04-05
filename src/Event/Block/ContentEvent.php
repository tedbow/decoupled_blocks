<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\ContentEvent.
 */

namespace Drupal\pdb\Event\Block;

use Drupal\pdb\Event\Block\BaseBlockEvent;

/**
 * Wraps block content for event listeners.
 */
class ContentEvent extends BaseBlockEvent {

  /**
   * @var array
   */
  protected $markup;

  public function __construct($component, array $markup) {
    parent::__construct($component);

    $this->markup = $markup;
  }

  public function getMarkup() {
    return $this->markup;
  }

  public function setMarkup(array $markup) {
    $this->markup = $markup;
  }

}
