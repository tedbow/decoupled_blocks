<?php

/**
 * @file
 * Contains \Drupal\pdb_ng2\EventSubscriber\Block\ContentSubscriber.
 */

namespace Drupal\pdb_ng2\EventSubscriber\Block;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\ContentEvent;

/**
 * Gets block content for the ng2 presentation.
 */
class ContentSubscriber implements EventSubscriberInterface {

  public function onBlockContent(ContentEvent $event) {
    $component = $event->getComponent();
    $markup = $event->getMarkup();

    $info = $component['info'];
    // Only override if presentation if ng2.
    if ($info['presentation'] == 'ng2') {
      // @TODO Find a better place for the stupid <app></app>... this
      // will add it for every block which is dumb. Ultimately we don't want
      // to need it at all.
      $markup['ng2'] = '<app></app><' . $info['machine_name'] . '></' . $info['machine_name'] . '>';
    }

    $event->setMarkup($markup);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PdbBlockEvents::CONTENT][] = array('onBlockContent');

    return $events;
  }

}
