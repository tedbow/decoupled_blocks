<?php

/**
 * @file
 * Contains \Drupal\pdb\Event\Block\PdbBlockEvents.
 */

namespace Drupal\pdb\Event\Block;

/**
 * Defines block events for the PDB module.
 */
final class PdbBlockEvents {

  /**
   * Name of the event fired when PDB block gets derivative markup.
   *
   * @Event
   *
   * @see \Drupal\pdb\Event\Block\ContentEvent
   *
   * @var string
   */
  const CONTENT = 'pdb.block.content';

  /**
   * Name of the event fired when PDB block gets derivative libraries.
   *
   * @Event
   *
   * @see \Drupal\pdb\Event\Block\LibraryEvent
   *
   * @var string
   */
  const LIBRARY = 'pdb.block.library';

  /**
   * Name of the event fired when PDB block gets derivative settings.
   *
   * @Event
   *
   * @see \Drupal\pdb\Event\Block\SettingEvent
   *
   * @var string
   */
  const SETTING = 'pdb.block.setting';

  /**
   * Name of the event fired when PDB block gets derivative html head elements.
   *
   * @Event
   *
   * @see \Drupal\pdb\Event\Block\HtmlHeadEvent
   *
   * @var string
   */
  const HTML_HEAD = 'pdb.block.html_head';

}
