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

    // TODO: We still need a way to decide if the entire
    // page supports angular2 components but this should be
    // part of the framework compatibility work.
    $pdb_block_deriver     = new PdbBlockDeriver('component_block');
    $pdb_block_definitions = $pdb_block_deriver->getDerivativeDefinitions();
    $components = array();

    foreach ($variant_blocks as $block_uuid => $block) {
      $block_name       = str_replace('component_block:', '', $block['id']);
      $block_definition = $pdb_block_definitions[$block_name];

      if ($block_definition['info']['presentation'] === 'ng2') {
        $module_uri_array = explode('/', $block_definition['info']['path']);
        array_pop($module_uri_array);

        $component_root = '/' . implode('/', $module_uri_array);
        $selector       = 'instance-id-' . $block_uuid;

        $components[$selector] = array(
          'uuid'           => $block_uuid,
          'uri'            => '/' . $block_definition['info']['path'],
          'component_root' => $component_root,
          'element'        => $block_name,
        );
      }
    }

    if (!empty($components)) {
      // Add the angular2 components to the drupalSettings array.
      $drupalSettings = $event->getDrupalSettings();
      $drupalSettings += array(
        'ng2' => array(
          'global_injectables' => array(),
          'components' => $components,
        ),
      );

      $event->setDrupalSettings($drupalSettings);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PdbFrameworkEvents::INITIALIZE][] = array('onFrameworkInitialize');

    return $events;
  }

}
