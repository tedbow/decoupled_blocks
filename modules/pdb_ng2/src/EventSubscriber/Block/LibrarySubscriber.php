<?php

/**
 * @file
 * Contains \Drupal\pdb_ng2\EventSubscriber\Block\LibrarySubscriber.
 */

namespace Drupal\pdb_ng2\EventSubscriber\Block;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\LibraryEvent;

/**
 * Gets block libraries for the ng2 presentation.
 */
class LibrarySubscriber implements EventSubscriberInterface {

  public function onBlockLibrary(LibraryEvent $event) {
    $component = $event->getComponent();
    $libraries = $event->getLibraries();

    $info = $component['info'];
    // Only override if presentation if ng2.
    if ($info['presentation'] == 'ng2') {
      if (!isset($libraries['pdb_ng2/angular2'])) {
        $libraries += array(
          'pdb_ng2/angular2'
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
