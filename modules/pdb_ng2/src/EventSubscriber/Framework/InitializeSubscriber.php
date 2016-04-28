<?php

/**
 * @file
 * Contains \Drupal\pdb_ng2\EventSubscriber\Framework\InitializeSubscriber.
 */

namespace Drupal\pdb_ng2\EventSubscriber\Framework;

use Drupal\pdb\Event\Framework\InitializeEvent;
use Drupal\pdb\Event\Framework\PdbFrameworkEvents;
use Drupal\pdb\Plugin\Derivative\PdbBlockDeriver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Initializes framework for the ng2 presentation.
 */
class InitializeSubscriber implements EventSubscriberInterface {

  public function onFrameworkInitialize(InitializeEvent $event) {
    $variant_blocks = $event->getControllerResult();

    $settings = array(
      'ng2' => array(
        'global_injectables' => array(),
        'components' => array(),
      ),
    );

    foreach ($variant_blocks as $block_uuid => $block) {
      if ($block['info']['presentation'] === 'ng2') {
        $settings['ng2']['components']['instance-id-' . $block_uuid] = [
          'uri' => '/' . $block['info']['path'],
          'element' => $block['info']['machine_name'],
        ];
      }
    }

    $drupalSettings = $event->getDrupalSettings();
    $drupalSettings += $settings;
    $event->setDrupalSettings($drupalSettings);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PdbFrameworkEvents::INITIALIZE][] = array('onFrameworkInitialize');

    return $events;
  }

}
