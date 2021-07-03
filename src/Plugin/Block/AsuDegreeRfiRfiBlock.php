<?php

namespace Drupal\asu_degree_rfi\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
// use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ASU Degree RFI module RFI Component block.
 *
 * @Block(
 *   id = "asu_degree_rfi_rfi_block",
 *   admin_label = @Translation("ASU Degree RFI form component"),
 * )
 */
class AsuDegreeRfiRfiBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Pass data from php:
    // https://codimth.com/blog/web/drupal/passing-data-php-javascript-drupal-8

    // Pull in block-level configs (see blockForm()). Drupal manages deltas.
    $config = $this->getConfiguration();

    // Rally props to pass to JS as drupalSettings.
    $props = [];

    // TODO rally settings here

    $block_output = [];
    // Markup containers where components will initialize.
    $block_output['#markup'] =
      $this->t('
        <!-- AsuRfi component will be initialized in this container. -->
        <div id="rfi-container">TODO RFI RENDERS HERE TODO</div>
        ');
    // $tag_menu = $config['asu_brand_header_block_menu_enabled'] ? $config['asu_brand_header_block_menu_name'] : 'main';
    // $block_output['#cache'] = [
    //   'contexts' => $this->getCacheContexts(),
    //   // Break cache when block or menus change.
    //   'tags' => Cache::mergeTags($this->getCacheTags(), Cache::buildTags('config:system.menu', [$tag_menu], '.')),
    // ];
    // Attach components and helper js registered in asu_brand.libraries.yml
    //$block_output['#attached']['library'][] = 'asu_brand/components-library';
    // Pass block configs to javascript. Gets taken up in js/asu_brand.header.js
    //$block_output['#attached']['drupalSettings']['asu_brand']['props'] = $props;
    // Get and pass cookie consent status, too.
    //$global_config = \Drupal::config('asu_brand.settings');
    //$block_output['#attached']['drupalSettings']['asu_brand']['cookie_consent'] = $global_config->get('asu_brand.asu_brand_cookie_consent_enabled');

    return $block_output;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // RFI component blocks are deployed in 1 of 2 ways:
    // 1. RFI form component blocks are automatically created and configured
    //    with on-demand Degree detail page creation.
    // 2. RFI form component blocks can be deployed as regular blocks via
    //    layout builder.

    /*
    Instances - One per degree? And one per individual deploy instance.
    Sourcing values... At on-demand creation set values to form. Or allow full config at indvidual deploy.

    RFI block config form
    College (college)
    Department (department)
    Campus (campus) [GROUND | ONLNE | NOPREF]
    Student Type (studentType) [undergrad | graduate]
    Area of Interest (areaOfInterest) - sourcing for this?
    Program of Interest (programOfInterest)
    Is this a Certificate or Minor? (isCertMinor) [true | false]
    Country (country) [iso 2 char country code] - sourcing?
    State or Province (stateProvince)
    Success page message (successMsg) [textarea with wysiwyg]
    Test mode (test) [true | false]
    TODO ... more configs (dataSource* fields) sourced from global admin settings

    TODO do we need AJAX API for service calls? Maybe not. We could just call
    here and do processing and build options.

    */

    // Config for this instance.
    $config = $this->getConfiguration();

    $form['asu_degree_rfi_college'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College code'),
      '#description' => $this->t('Enter a valid college code to filter degree listings. To identify college codes refer to the <a href="https://api.myasuplat-dpl.asu.edu/api/codeset/colleges" target="_blank">college data service</a> and find the acadOrgCode that matches your college. Note: you may want to install a JSON formatter browser extension or paste the page output into an online JSON formatter to render the data human-readable.'),
      '#default_value' => isset($config['asu_degree_rfi_college']) ?
        $config['asu_degree_rfi_college'] : null
    ];
    $form['asu_degree_rfi_department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department code'),
      '#description' => $this->t('Enter a valid department code to filter degree listings. To identify department codes refer to the <a href="https://degreesearch-proxy.apps.asu.edu/degreesearch/?method=findAllDegrees&init=false&fields=DepartmentCode,DepartmentName&cert=false&program=undergrad" target="_blank">undergraduate degree departments data service</a> or the <a href="https://degreesearch-proxy.apps.asu.edu/degreesearch/?method=findAllDegrees&init=false&fields=DepartmentCode,DepartmentName&cert=false&program=graduate" target="_blank">graduate degree departments data service</a> to find a DepartmentCode that matches your department\'s name. Note: you may want to install a JSON formatter browser extension or paste the page output into an online JSON formatter to render the data human-readable.'),
      '#default_value' => isset($config['asu_degree_rfi_department']) ?
        $config['asu_degree_rfi_department'] : null
    ];
    $form['asu_degree_rfi_campus'] = [
      '#type' => 'select',
      '#title' => $this->t('Campus'),
      '#description' => $this->t('Preconfigure RFI for a campus choice.'),
      '#options' => [
        '' => $this->t('none'),
        'GROUND' => $this->t('On campus'),
        'ONLNE' => $this->t('Online'),
        'NOPREF' => $this->t('Not sure'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_campus']) ?
        $config['asu_degree_rfi_campus'] : '',
    ];
    $form['asu_degree_rfi_student_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Student type'),
      '#description' => $this->t('Preconfigure RFI with a student type.'),
      '#options' => [
        '' => $this->t('none'),
        'undergrad' => $this->t('Undergraduate'),
        'graduate' => $this->t('Graduate'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_student_type']) ?
        $config['asu_degree_rfi_student_type'] : '',
    ];
    // TODO ajaxify .... or turn into text field
    $form['asu_degree_rfi_area_of_interest'] = [
      '#type' => 'select',
      '#title' => $this->t('Area of interest'),
      '#description' => $this->t('Preconfigure RFI with an area of interest.'),
      '#options' => [
        '' => $this->t('none'),
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_area_of_interest']) ?
        $config['asu_degree_rfi_area_of_interest'] : '',
    ];
    // TODO ajaxify .... or turn into text field
    $form['asu_degree_rfi_program_of_interest'] = [
      '#type' => 'select',
      '#title' => $this->t('Program of interest'),
      '#description' => $this->t('Preconfigure RFI with a program of interest.'),
      '#options' => [
        '' => $this->t('none'),
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_program_of_interest']) ?
        $config['asu_degree_rfi_program_of_interest'] : '',
    ];
    $form['asu_degree_rfi_is_cert_minor'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is a certificate or minor'),
      '#description' => $this->t('Currently, certificates and minors are not
        rendered with a form and instead display the Success page message along
        with the email for the program.'),
      '#default_value' => isset($config['asu_degree_rfi_is_cert_minor']) ?
        $config['asu_degree_rfi_is_cert_minor'] : 0,
    ];
    // TODO ajaxify .... or turn into text field with note about ISO 2 char country codes
    $form['asu_degree_rfi_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#description' => $this->t('Preconfigure RFI for submissions from a specific country.'),
      '#options' => [
        '' => $this->t('none'),
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_country']) ?
        $config['asu_degree_rfi_country'] : '',
    ];
    // TODO ajaxify .... or turn into text field with note about ISO 2 char country codes
    $form['asu_degree_rfi_state_province'] = [
      '#type' => 'select',
      '#title' => $this->t('State or province'),
      '#description' => $this->t('Preconfigure RFI for submissions from a specific US state or Canadian province.'),
      '#options' => [
        '' => $this->t('none'),
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3'),
      ],
      '#default_value' => isset($config['asu_degree_rfi_state_province']) ?
        $config['asu_degree_rfi_state_province'] : '',
    ];
    $form['asu_brand_header_success_msg'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Success page message'),
      '#description' => $this->t('Optional. Provide a custom success message to display after submit. Also used to customize the certificate and minor display.'),
      '#default_value' => isset($config['asu_degree_rfi_success_msg']['value']) ?
        $config['asu_degree_rfi_success_msg']['value'] : '',
      '#format' => 'basic_html',
      '#allowed_formats' => ['basic_html'],
    ];
    $form['asu_degree_rfi_test'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Run in test mode'),
      '#description' => $this->t('Check to launch the form in test mode. Submissions will have a test flag set.'),
      '#default_value' => isset($config['asu_degree_rfi_test']) ?
        $config['asu_degree_rfi_test'] : 0,
    ];

    // Note additional prop values for component launch will be sourced from
    // global admin settings.

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $values = $form_state->getValues();

    $this->configuration['asu_degree_rfi_college'] =
      $values['asu_degree_rfi_college'];
    $this->configuration['asu_degree_rfi_department'] =
      $values['asu_degree_rfi_department'];
    $this->configuration['asu_degree_rfi_campus'] =
      $values['asu_degree_rfi_campus'];
    $this->configuration['asu_degree_rfi_student_type'] =
      $values['asu_degree_rfi_student_type'];
    $this->configuration['asu_degree_rfi_area_of_interest'] =
      $values['asu_degree_rfi_area_of_interest'];
    $this->configuration['asu_degree_rfi_program_of_interest'] =
      $values['asu_degree_rfi_program_of_interest'];
    $this->configuration['asu_degree_rfi_is_cert_minor'] =
      $values['asu_degree_rfi_is_cert_minor'];
    $this->configuration['asu_degree_rfi_country'] =
      $values['asu_degree_rfi_country'];
    $this->configuration['asu_degree_rfi_state_province'] =
      $values['asu_degree_rfi_state_province'];
    $this->configuration['asu_degree_rfi_success_msg'] =
      $values['asu_brand_header_success_msg'];
    $this->configuration['asu_degree_rfi_test'] =
      $values['asu_degree_rfi_test'];
  }
}
