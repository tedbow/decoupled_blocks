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
  protected $configuration;

  /**
   * @var array
   */
  protected $markup;

  public function __construct($component, array $configuration, array $markup) {
    parent::__construct($component);

    $this->configuration = $configuration;
    $this->markup = $markup;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

  public function getMarkup() {
    return $this->markup;
  }

  public function setMarkup(array $markup) {
    $this->markup = $markup;
  }

}
