<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\BaseBlockEvent.
 */

namespace Drupal\pdb\Event\Block;

use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps block plugin definition for event listeners.
 */
abstract class BaseBlockEvent extends Event {

  /**
   * @var array
   */
  protected $component;

  public function __construct($component) {
    $this->component = $component;
  }

  public function getComponent() {
    return $this->component;
  }

  public function setComponent($component) {
    $this->component = $component;
  }

}
