<?php

/**
 * @file
 * Contains \Drupal\pdp\Plugin\Derivative\PdpBlockDeriver.
 */

namespace Drupal\pdp\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\pdp\Plugin\Extension\PdpExtensionDiscovery;
use Drupal\Core\Extension\InfoParser;

/**
 * Provides a deriver for pdp blocks.
 */
class PdpBlockDeriver extends DeriverBase implements ContainerDeriverInterface {
  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * Constructs a PdpBlockDeriver instance.
   */
  public function __construct($base_plugin_id) {
    $this->basePluginId = $base_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Get all custom blocks which should be rediscovered.
    $components = $this->_pdp_rebuild_component_data();
    foreach ($components as $block_id => $block_info) {
      $this->derivatives[$block_id] = $base_plugin_definition;
      $this->derivatives[$block_id]['info'] = $block_info->info;
      $this->derivatives[$block_id]['admin_label'] = $block_info->info['name'];
      $this->derivatives[$block_id]['cache'] = DRUPAL_NO_CACHE;
    }
    return $this->derivatives;
  }

  /**
   * Helper function to scan and collect component .info.yml data.
   *
   * This is based on system.module function _system_rebuild_module_data().
   *
   * @TODO: The results of this need to be cached.
   *
   * @return \Drupal\Core\Extension\Extension[]
   *   An associative array of component information.
   */
  private function _pdp_rebuild_component_data() {

    $listing = new PdpExtensionDiscovery(\Drupal::root());

    // Find components.
    $components = $listing->scan('pdp');

    // Set defaults for module info.
    $defaults = array(
      'dependencies' => array(),
      'description' => '',
      'package' => 'Other',
      'version' => NULL,
    );

    // Read info files for each module.
    foreach ($components as $key => $component) {
      // Look for the info file.
      $component->info = \Drupal::service('info_parser')->parse($component->getPathname());
      $component->info['path'] = $component->origin . '/' . $component->subpath;

      // Merge in defaults and save.
      $components[$key]->info = $component->info + $defaults;

      \Drupal::moduleHandler()->alter('component_info', $components[$key]->info, $components[$key]);
    }

    dpm($components, 'final components');

    return $components;
  }

}
