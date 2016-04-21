<?php

/**
 * @file
 * Contains \Drupal\pdb_react\EventSubscriber\Block\LibrarySubscriber.
 */

namespace Drupal\pdb_react\EventSubscriber\Block;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\LibraryEvent;

/**
 * Gets block libraries for the react presentation.
 */
class LibrarySubscriber implements EventSubscriberInterface {

  public function onBlockLibrary(LibraryEvent $event) {
    $component = $event->getComponent();
    $libraries = $event->getLibraries();

    $info = $component['info'];
    // Only override if presentation if react.
    if ($info['presentation'] == 'react') {
      if (!isset($libraries['pdb_react/react'])) {
        $libraries += array(
          'pdb_react/react',
          'pdb_react/components',
        );
      }
    }

    $event->setLibraries($libraries);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PdbBlockEvents::LIBRARY][] = array('onBlockLibrary');

    return $events;
  }

}
