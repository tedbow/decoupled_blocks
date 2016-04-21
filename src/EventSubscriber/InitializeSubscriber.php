<?php

/**
 * @file
 * Contains \Drupal\pdb\EventSubscriber\InitializeSubscriber.
 */

namespace Drupal\pdb\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Drupal\pdb\Event\Framework\InitializeEvent;
use Drupal\pdb\Event\Framework\PdbFrameworkEvents;

/**
 * Gets variant information and allows pdb modules to initialize its framework.
 */
class InitializeSubscriber implements EventSubscriberInterface {

  public function onKernelView(GetResponseForControllerResultEvent $event, $name, EventDispatcherInterface $dispatcher) {
    $controller_result = $event->getControllerResult();

    if (isset($controller_result['#page_variant'])) {
      $variant = $controller_result['#page_variant'];
      $variant_plugins  = $variant->getPluginCollections();
      $variant_settings = $variant_plugins['variant_settings']->getConfiguration();

      $variant_blocks = array();
      if (isset($variant_settings['blocks'])) {
        $variant_blocks = $variant_settings['blocks'];
      }

      $initialize_event = new InitializeEvent($variant_blocks);

      $dispatcher->dispatch(PdbFrameworkEvents::INITIALIZE, $initialize_event);

      $drupalSettings = $initialize_event->getDrupalSettings();
      if (!isset($controller_result['#attached']['drupalSettings'])) {
        $controller_result['#attached']['drupalSettings'] = array();
      }
      $controller_result['#attached']['drupalSettings'] += $drupalSettings;

      $event->setControllerResult($controller_result);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // We need our subscriber to execute before others, so use priority 10.
    $events[KernelEvents::VIEW][] = array('onKernelView', 10);

    return $events;
  }

}
