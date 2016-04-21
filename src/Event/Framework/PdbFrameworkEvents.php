<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Framework\PdbFrameworkEvents.
 */

namespace Drupal\pdb\Event\Framework;

/**
 * Defines framework events for the PDB module.
 */
final class PdbFrameworkEvents {

  /**
   * Name of the event fired when PDB framework gets initialization config.
   *
   * @Event
   *
   * @see \Drupal\pdb\Event\Framework\InitializeEvent
   *
   * @var string
   */
  const INITIALIZE = 'pdb.framework.initialize';

}
