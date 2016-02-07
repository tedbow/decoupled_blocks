<?php
namespace Drupal\pdb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Component' block.
 *
 * @Block(
 *   id = "component_block",
 *   admin_label = @Translation("Component block"),
 *   deriver = "Drupal\pdb\Plugin\Derivative\PdbBlockDeriver"
 * )
 */
class PdbBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $name = $this->getDerivativeId();
    $component = $this->getPluginDefinition();
    $markup = $this->getDerivativeMarkup($component);
    $attached = $this->getDerivativeAttachments($component);

    return array(
      // @TODO Find a better place for the stupid <app></app>... this
      // will add it for every block which is dumb. Ultimately we don't want
      // to need it at all.
      '#markup' => '<app></app>' . $markup,
      '#allowed_tags' => array('app', $name),
      // What gets attached should depend not just on framework module enabled,
      // but also the one currently selected for a panel variant .
      '#attached' => $attached,
    );
  }

  /**
   * Put together all attachments for this component.
   */
  public function getDerivativeAttachments($component) {
    $return = array();

    // Get any necessary libraries and add them to #attached.
    $return['library'] = $this->getDerivativeLibrary($component);

    // Get any necessary drupalSettings and add them to #attached.
    $return['drupalSettings'] = $this->getDerivativeSettings($component);

    return $return;
  }

  /**
   * Put together any libraries needed.
   */
  public function getDerivativeLibrary($component) {
    // @TODO: This needs to be an event dispatcher so framework modules can
    // subscribe to it and add their stuff. Using antiquated hooks for now.
    $libraries = array();

    $override = \Drupal::service('module_handler')->invokeAll('pdb_libraries', array($component, $libraries));

    $libraries += $override;

    return $libraries;
  }

  /**
   * Put together any specific settings for exposing to the front end.
   */
  public function getDerivativeSettings($component) {
    // @TODO: This needs to be an event dispatcher so framework modules can
    // subscribe to it and add their stuff. Using antiquated hooks for now.
    $settings = array();

    $override = \Drupal::service('module_handler')->invokeAll('pdb_settings', array($component, $settings));

    $settings += $override;

    return $settings;
  }

  /**
   * What we render server side, most likely to be taken over by client.
   */
  public function getDerivativeMarkup($component) {
    // @TODO: This needs to be an event dispatcher so framework modules can
    // subscribe to it and add their stuff. Using antiquated hooks for now.
    $return = 'This is default content';

    $override = \Drupal::service('module_handler')->invokeAll('pdb_markup_override', array($component, $return));

    if (!empty($override) && count($override) === 1) {
      $return = array_pop($override);
    }
    // If we've got multiple successful overrides, what do we do?
    else if (!empty($override)) {
      // Throw an error at least, we're in bat country.
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // @TODO: This is useless right now. In our original module we had a
    // JSON-ified version of the form API passing necessary fields into Drupal.
    // A similar approach is necessary.
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    // Add a form field to the existing block configuration form.
    $form['example'] = array(
      '#type' => 'textfield',
      '#title' => t('example'),
      '#default_value' => isset($config['example']) ? $config['example'] : '',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // Nothing yet.
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('example', $form_state->getValue('example'));
  }
}
?>