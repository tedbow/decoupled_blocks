<?php

/**
 * @file
 * Contains \Drupal\pdb_react\EventSubscriber\Block\SettingSubscriber.
 */

namespace Drupal\pdb_react\EventSubscriber\Block;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\SettingEvent;

/**
 * Gets block settings for the react presentation.
 */
class SettingSubscriber implements EventSubscriberInterface {

  public function onBlockSetting(SettingEvent $event) {
    $component = $event->getComponent();
    $settings  = $event->getSettings();

    $info = $component['info'];
    if ($info['presentation'] == 'react') {
      if (!isset($settings['react-apps'])) {
        $settings['react-apps'] = array();
      }
      $settings['react-apps'][$info['machine_name']] = array(
        'uri' => $info['path']
      );
    }

    $event->setSettings($settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PdbBlockEvents::SETTING][] = array('onBlockSetting');

    return $events;
  }

}
