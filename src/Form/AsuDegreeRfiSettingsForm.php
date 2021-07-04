<?php

/**
 * @file
 * Conttains Drupal\asu_degree_rfi\Form\AsuDegreeRfiSettingsForm
 */

namespace Drupal\asu_degree_rfi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Configure example settings for this site.
 */
class AsuDegreeRfiSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'asu_degree_rfi.settings'; // Config variable name for module.

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Returns the form's unique ID.
    return 'asu_degree_rfi_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // Gets the configuration name.
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Build and return the form array.

    // Loads admin settings for this form/module.
    $config = $this->config(static::SETTINGS);

    // Note: asu_brand module provides both global and per-block configs.

    // Global configs

    $form['asu_degree_rfi']['asu_degree_rfi_degree_instructions'] = [
      '#type' => 'item',
      '#title' => $this->t("About the ASU Degree RFI module"),
      '#description' => $this->t("This module provides the degree listing and
        degree detail content types as well as blocks and configurations to
        enable their usage. Simply create one or more degree listing pages, and
        the first time you visit the degrees listed there, they will be created
        automatically in your site. The content is loaded from the external
        Degree Search service, but both the degree listing and degree details
        pages allow you to override select content to make it your own. Because
        current degrees appearing in the listing page will change over time,
        degree detail pages periodically check with the Degree Search service,
        and will unpublish the page if it is no longer available. For most
        sites, the default configurations for degrees are recommended."),
    ];
    $form['asu_degree_rfi']['asu_degree_rfi_rfi_instructions'] = [
      '#type' => 'item',
      '#title' => $this->t("Using RFIs (prospective student Request for
        Information forms)"),
      '#description' => $this->t("The prospective student Request for
        Information (RFI) forms supplied by this module submit to to ASU's RFI
        handling systems which route submission to appropriate Salesforce
        destinations. Before you can begin using the RFI form, you must first
        obtain a Source ID. Please visit TODO TODO TODO
        For most sites, using the configuration defaults are recommended."),
    ];

    // RFI
    // - RFI Source ID
    // - Degree Search data source URL
    // - ASUOnline data source URL
    // - Countries and States data source URL
    // TODO to be implemented via api_proxy: SubmissionUrl

    $form['asu_degree_rfi']['rfi'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('RFI components'),
    );
    $form['asu_degree_rfi']['rfi']['rfi_source_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source ID'),
      '#default_value' => $config->get('asu_degree_rfi.rfi_source_id'),
      '#description' => $this->t("TODO TODO TODO You must provide a valid source ID to submit RFIs. TODO TODO TODO"),
      '#required' => TRUE,
    ];
    $form['asu_degree_rfi']['rfi']['rfi_degree_search_datasource_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RFI Degree Search data source URL'),
      '#default_value' => $config->get('asu_degree_rfi.rfi_degree_search_datasource_endpoint'),
      '#description' => $this->t("Recommended to be left blank to use default data source (https://degreesearch-proxy.apps.asu.edu/degreesearch/) defined internally by the RFI component."),
    ];
    $form['asu_degree_rfi']['rfi']['rfi_asuonline_datasource_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RFI ASUOnline data source URL'),
      '#default_value' => $config->get('asu_degree_rfi.rfi_asuonline_datasource_endpoint'),
      '#description' => $this->t("Recommended to be left blank to use default data source (https://asuonline.asu.edu/lead-submissions-v3.3/programs) defined internally by the RFI component."),
    ];
    $form['asu_degree_rfi']['rfi']['rfi_country_province_datasource_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RFI countries and states data source URL'),
      '#default_value' => $config->get('asu_degree_rfi.rfi_country_province_datasource_endpoint'),
      '#description' => $this->t("Recommended to be left blank to use default data source (https://api.myasuplat-dpl.asu.edu/api/codeset/countries) defined internally by the RFI component."),
    ];

    // DEGREES
    // programList
    // - endpoint
    // - method
    // - init
    // exclude following, sourced from node
    // - college programList.dataSource.collegeAcadOrg
    // - department programList.dataSource.collegeAcadOrg
    // - program programList.dataSource.program
    // - certs and minors programList.dataSource.cert
    $form['asu_degree_rfi']['degree'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Degree components'),
    );
    $form['asu_degree_rfi']['degree']['program_list_datasource_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree list data source URL'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_list_datasource_endpoint'),
      '#description' => $this->t("Recommended to be left blank to use default data source (https://degreesearch-proxy.apps.asu.edu/degreesearch/) defined internally by the degree list component."),
    ];
    $form['asu_degree_rfi']['degree']['program_list_datasource_method'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree list data source method'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_list_datasource_method'),
      '#description' => $this->t("Recommended to be left blank to use default data source method (findAllDegrees) defined internally by the degree list component."),
    ];
    $form['asu_degree_rfi']['degree']['program_list_datasource_init'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree list data source init value'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_list_datasource_init'),
      '#description' => $this->t("Recommended to be left blank to use default data source init value (false) defined internally by the degree list component."),
    ];
    // dataSource
    // - endpoint
    // - method
    // - init
    // exclude following, sourced from node
    // - Academic plan code dataSource.acadPlan
    $form['asu_degree_rfi']['degree']['program_detail_datasource_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree detail data source URL'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_detail_datasource_endpoint'),
      '#description' => $this->t("Recommended to be left blank to use default data source (https://degreesearch-proxy.apps.asu.edu/degreesearch/) defined internally by the degree list component."),
    ];
    $form['asu_degree_rfi']['degree']['program_detail_datasource_method'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree detail data source method'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_detail_datasource_method'),
      '#description' => $this->t("Recommended to be left blank to use default data source method (findDegreeByAcadPlan) defined internally by the degree list component."),
    ];
    $form['asu_degree_rfi']['degree']['program_detail_datasource_init'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree detail data source init value'),
      // TODO confirm this appropriately is left unset - or do we need to set null with ternary?
      '#default_value' => $config->get('asu_degree_rfi.program_detail_datasource_init'),
      '#description' => $this->t("Recommended to be left blank to use default data source init value (false) defined internally by the degree list component."),
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Process the form submission.

    // Break RFI block cache when we save.
    Cache::invalidateTags(['rfi_block_cache']);

    // Note: Use asu_degree_rfi prefix when handling config values, but not with
    // form_state versions of those values.

    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configurations on our config.
      ->set('asu_degree_rfi.program_list_datasource_endpoint', $form_state->getValue('program_list_datasource_endpoint'))
      ->set('asu_degree_rfi.program_list_datasource_method', $form_state->getValue('program_list_datasource_method'))
      ->set('asu_degree_rfi.program_list_datasource_init', $form_state->getValue('program_list_datasource_init'))
      ->set('asu_degree_rfi.program_detail_datasource_endpoint', $form_state->getValue('program_detail_datasource_endpoint'))
      ->set('asu_degree_rfi.program_detail_datasource_method', $form_state->getValue('program_detail_datasource_method'))
      ->set('asu_degree_rfi.program_detail_datasource_init', $form_state->getValue('program_detail_datasource_init'))
      ->set('asu_degree_rfi.rfi_source_id', $form_state->getValue('rfi_source_id'))
      ->set('asu_degree_rfi.rfi_degree_search_datasource_endpoint', $form_state->getValue('rfi_degree_search_datasource_endpoint'))
      ->set('asu_degree_rfi.rfi_asuonline_datasource_endpoint', $form_state->getValue('rfi_asuonline_datasource_endpoint'))
      ->set('asu_degree_rfi.rfi_country_province_datasource_endpoint', $form_state->getValue('rfi_country_province_datasource_endpoint'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
