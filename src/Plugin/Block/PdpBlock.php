<?php
namespace Drupal\pdp\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Component' block.
 *
 * @Block(
 *   id = "component_block",
 *   admin_label = @Translation("Component block"),
 *   deriver = "Drupal\pdp\Plugin\Derivative\PdpBlockDeriver"
 * )
 */
class PdpBlock extends BlockBase {

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
      // but also the one currently selected for a variant.
      '#attached' => $attached,
    );
  }

  /**
   *
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
   *
   */
  public function getDerivativeLibrary($component) {
    // This needs to be a listener or hook or whatever.
    return array(
      'pdp_ng2/angular2'
    );
  }

  /**
   *
   */
  public function getDerivativeSettings($component) {
    // This needs also to be a listener or hook or whatever.
    return array(
      'apps' => array(
        $component['info']['machine_name'] => array(
          'uri' => $component['info']['path'],
        )
      )
    );
  }

  /**
   *
   */
  public function getDerivativeMarkup($component) {
    // This must be framework-based.
    $name = $this->getDerivativeId();
    $return = '<' . $name . '></' . $name . '>';

    \Drupal::moduleHandler()->alter('pdp_markup_alter', $component, $return);

    return $return;
  }

    /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
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
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('example', $form_state->getValue('example'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // Nothing yet.
  }
}
?>