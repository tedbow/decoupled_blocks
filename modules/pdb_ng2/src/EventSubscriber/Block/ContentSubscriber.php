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
    $configuration = $event->getConfiguration();
    $markup = $event->getMarkup();

    $info = $component['info'];
    // Only override if presentation if ng2.
    if ($info['presentation'] == 'ng2') {
      $uuid = $configuration['uuid'];
      $markup['ng2'] = '<' . $info['machine_name'] . ' id="instance-id-' . $uuid . '">';
      $markup['ng2'] .= '</' . $info['machine_name'] . '>';
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
