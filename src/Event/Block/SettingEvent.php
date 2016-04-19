<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\SettingEvent.
 */

namespace Drupal\pdb\Event\Block;

use Drupal\pdb\Event\Block\BaseBlockEvent;

/**
 * Wraps block settings for event listeners.
 */
class SettingEvent extends BaseBlockEvent {

  /**
   * @var array
   */
  protected $settings;

  public function __construct($component, array $settings) {
    parent::__construct($component);

    $this->settings = $settings;
  }

  public function getSettings() {
    return $this->settings;
  }

  public function setSettings(array $settings) {
    $this->settings = $settings;
  }

}
