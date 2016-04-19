<?php
namespace Drupal\pdb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

use Drupal\pdb\Event\Block\ContentEvent;
use Drupal\pdb\Event\Block\HtmlHeadEvent;
use Drupal\pdb\Event\Block\LibraryEvent;
use Drupal\pdb\Event\Block\PdbBlockEvents;
use Drupal\pdb\Event\Block\SettingEvent;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a 'Component' block.
 *
 * @Block(
 *   id = "component_block",
 *   admin_label = @Translation("Component block"),
 *   deriver = "Drupal\pdb\Plugin\Derivative\PdbBlockDeriver"
 * )
 */
class PdbBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  private $dispatcher;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EventDispatcherInterface $dispatcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->dispatcher = $dispatcher;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $dispatcher = $container->get('event_dispatcher');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $dispatcher
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $name = $this->getDerivativeId();
    $component = $this->getPluginDefinition();
    $markup = $this->getDerivativeMarkup($component);
    $attached = $this->getDerivativeAttachments($component);

    return array(
      '#markup' => $markup,
      '#allowed_tags' => array('div', 'app', $name),
      // What gets attached should depend not just on framework module enabled,
      // but also the one currently selected for a panel variant .
      '#attached' => $attached,
    );
  }

  /**
   * What we render server side, most likely to be taken over by client.
   */
  public function getDerivativeMarkup($component) {
    $markup = array();
    $markup['default'] = 'This is default content';
    $key = $component['info']['presentation'];

    $content_event = new ContentEvent($component, $markup);

    $this->dispatcher->dispatch(PdbBlockEvents::CONTENT, $content_event);

    $derivative_markup = $content_event->getMarkup();

    if (isset($derivative_markup[$key])) {
      $markup = $derivative_markup[$key];
    }
    // If we've got multiple successful overrides, what do we do?
    elseif (!empty($derivative_markup)) {
      // Throw an error at least, we're in bat country.
      drupal_set_message('we are in bat country', 'error');
      $markup = $derivative_markup['default'];
    }

    return $markup;
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

    // In some cases we may need to attach directly to html_head.
    $return['html_head'] = $this->getDerivativeHtmlHead($component);

    return $return;
  }

  /**
   * Put together any libraries needed.
   */
  public function getDerivativeLibrary($component) {
    $libraries = array();

    $library_event = new LibraryEvent($component, $libraries);

    $this->dispatcher->dispatch(PdbBlockEvents::LIBRARY, $library_event);

    $derivative_libraries = $library_event->getLibraries();

    $libraries += $derivative_libraries;

    return $libraries;
  }

  /**
   * Put together any specific settings for exposing to the front end.
   */
  public function getDerivativeSettings($component) {
    $settings = array();

    $setting_event = new SettingEvent($component, $settings);

    $this->dispatcher->dispatch(PdbBlockEvents::SETTING, $setting_event);

    $derivative_setting = $setting_event->getSettings();

    $settings += $derivative_setting;

    return $settings;
  }

  /**
   * Put together any html_head attachments.
   */
  public function getDerivativeHtmlHead($component) {
    $html_head = array();

    $html_head_event = new HtmlHeadEvent($component, $html_head);

    $this->dispatcher->dispatch(PdbBlockEvents::HTML_HEAD, $html_head_event);

    $derivative_html_head = $html_head_event->getHtmlHead();

    $html_head += $derivative_html_head;

    return $html_head;
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
