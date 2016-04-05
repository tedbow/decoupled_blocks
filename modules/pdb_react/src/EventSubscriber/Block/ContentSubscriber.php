<?php

/**
 * @file
 * Contains \Drupal\pdb_react\EventSubscriber\Block\ContentSubscriber.
 */

namespace Drupal\pdb_react\EventSubscriber\Block;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\ContentEvent;

/**
 * Gets block content for the react presentation.
 */
class ContentSubscriber implements EventSubscriberInterface {

  public function onBlockContent(ContentEvent $event) {
    $component = $event->getComponent();
    $markup  = $event->getMarkup();

    $info = $component['info'];
    // Only override if presentation if react.
    if ($info['presentation'] === 'react') {
      $react_markup  = '<' . $info['machine_name'] . ' id="' . $info['machine_name'] . '">';
      $react_markup .= '</' . $info['machine_name'] . '>';

      $markup['react'] = $react_markup;
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
