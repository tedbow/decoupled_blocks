<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\LibraryEvent.
 */

namespace Drupal\pdb\Event\Block;

use Drupal\pdb\Event\Block\BaseBlockEvent;

/**
 * Wraps block libraries for event listeners.
 */
class LibraryEvent extends BaseBlockEvent {

  /**
   * @var array
   */
  protected $libraries;

  public function __construct($component, array $libraries) {
    parent::__construct($component);

    $this->libraries = $libraries;
  }

  public function getLibraries() {
    return $this->libraries;
  }

  public function setLibraries(array $libraries) {
    $this->libraries = $libraries;
  }

}
