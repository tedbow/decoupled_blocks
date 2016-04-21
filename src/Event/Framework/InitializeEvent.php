<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Framework\InitializeEvent.
 */

namespace Drupal\pdb\Event\Framework;

use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps framework configuration for event listeners.
 */
class InitializeEvent extends Event {

  /**
   * @var array
   */
  protected $controllerResult;

  /**
   * @var array
   */
  protected $drupalSettings;

  public function __construct(array $controllerResult) {
    $this->controllerResult = $controllerResult;

    $this->drupalSettings = array();
  }

  public function getControllerResult() {
    return $this->controllerResult;
  }

  public function setControllerResult(array $controllerResult) {
    $this->controllerResult = $controllerResult;
  }

  public function getDrupalSettings() {
    return $this->drupalSettings;
  }

  public function setDrupalSettings(array $drupalSettings) {
    $this->drupalSettings = $drupalSettings;
  }

}
