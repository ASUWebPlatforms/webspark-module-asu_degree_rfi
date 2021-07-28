<?php

namespace Drupal\asu_degree_rfi\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * ASU Degree RFI module Degree details component block.
 *
 * @Block(
 *   id = "asu_degree_rfi_degree_details_block",
 *   admin_label = @Translation("Degree details component"),
 * )
 */
class AsuDegreeRfiDegreeDetailsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Default block caching works for now. Cache is invalidated when
    // node is updated and that fits our use-case.
    // Define cache tag.
    // Gets invalidated when ... TBD
    return Cache::mergeTags(parent::getCacheTags(), array('degree_details_block_cache'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Note: See implementation details regarding degree component blocks in
    // the Degree listing block build() function.

    // Pass data from php:
    // https://codimth.com/blog/web/drupal/passing-data-php-javascript-drupal-8

    // Pull in global configs for module.
    $global_config = \Drupal::config('asu_degree_rfi.settings');

    // Pull in block-level configs (see blockForm()). Drupal manages deltas.
    $config = $this->getConfiguration();

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node && !($node instanceof \Drupal\node\NodeInterface || $node->bundle() == 'degree_detail_page')) {
      return;
    }

    // Rally props to pass to JS as drupalSettings.
    $props = [];

    //Default images.
    $props['appPathFolder'] = \Drupal::service('asu_degree_rfi.helper_functions')->getappPathFolder();

    $dataSource = new \stdClass();
    if ($global_config->get('asu_degree_rfi.program_detail_datasource_endpoint')) {
      $dataSource->endpoint = $global_config->get('asu_degree_rfi.program_detail_datasource_endpoint');
    }
    if ($global_config->get('asu_degree_rfi.program_detail_datasource_method')) {
      $dataSource->method = $global_config->get('asu_degree_rfi.program_detail_datasource_method');
    }
    if ($global_config->get('asu_degree_rfi.program_detail_datasource_init')) {
      $dataSource->init = (bool)$global_config->get('asu_degree_rfi.program_detail_datasource_init');
    }
    $dataSource->acadPlan = $node->field_degree_detail_acadplancode->value;
    $props['dataSource'] = $dataSource;

    //Hero
    $hero = new \stdClass();
    $image = \Drupal::service('asu_degree_rfi.helper_functions')->getImageFieldValue($node->field_degree_detail_hero_image);
    if (!empty((array)$image)) {
      $hero->image = $image;
    }
    if ($node->field_degree_detail_hero_size->value) {
      $hero->image->size = $node->field_degree_detail_hero_size->value;
    }
    if ($node->field_degree_detail_hero_title->value) {
      $hero->title->text = $node->field_degree_detail_hero_title->value;
    }
    if ($node->field_degree_detail_hero_highlit->value) {
      $hero->title->highlightColor = $node->field_degree_detail_hero_highlit->value;
    }
    if (!empty((array)$hero)) {
      $props['hero'] = $hero;
    }

    //Intro Content
    $introContent = new \stdClass();

    $breadcrumbs = [];
    foreach ($node->field_degree_detail_breadcrumbs as $breadcrumbs_link) {
      $item_breadcrumbs = new \stdClass();
      $item_breadcrumbs->text = $breadcrumbs_link->title;
      $link = Url::fromUri($breadcrumbs_link->uri);
      $item_breadcrumbs->url = $link->toString();
      $breadcrumbs[] = $item_breadcrumbs;
    }

    //Set default breacdcrumbs.
    if (empty($breadcrumbs)) {
      $breadcrumbs[] = (object) [
        'text' => 'Home',
        'url' => Url::fromRoute('<front>')->toString(),
      ];
      $url = Url::fromRoute('<current>')->toString();
      $split = explode('/', $url);

      if (isset($split[6]) && is_numeric($split[6])) {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $listing_page_node = $node_storage->load($split[6]);
        if ($node) {
          $breadcrumbs[] = (object) [
            'text' => $listing_page_node->getTitle(),
            'url' => Url::fromRoute('entity.node.canonical', ['node' => $listing_page_node->id()])->toString(),
          ];
        }
      }
    }
    //Add active link
    //@TODO get title from Academic plan code
    $breadcrumbs[] = (object) [
      'text' => 'Detail Page',
      'isActive' => true,
    ];
    $introContent->breadcrumbs = $breadcrumbs;

    if ($node->field_degree_detail_intro_media->target_id) {
      $bundle = $node->field_degree_detail_intro_media->entity->bundle();
      switch ($bundle) {
        case 'image':
          $image = \Drupal::service('asu_degree_rfi.helper_functions')->getImageFieldValue($node->field_degree_detail_intro_media);
          if (!empty((array)$image)) {
            $introContent->image = $image;
          }
          break;
        case 'video':
          $video = \Drupal::service('asu_degree_rfi.helper_functions')->getVideoFieldValue($node->field_degree_detail_intro_media);
          if (!empty((array)$video)) {
            $introContent->video = $video;
          }
          break;
      }
    }

    if ($node->field_degree_dtl_intro_content->value) {
      $introContent->contents[] = (object) [
        'text' => $node->field_degree_dtl_intro_content->value,
      ];
    }
    $intro_content_hide_fields = [
      'hideMarketText' => 'field_deg_dtl_intro_hide_market',
      'hideProgramDesc' => 'field_deg_dtl_intro_hide_pgmdesc',
      'hideRequiredCourses' => 'field_deg_dtl_intro_hide_req_crs',
    ];
    foreach ($intro_content_hide_fields as $key => $hide_field) {
      if ($node->{$hide_field}) {
        $introContent->{$key} = (bool) $node->{$hide_field}->value;
      }
    }

    $props['introContent'] = $introContent;

    //AtAGlance
    $atAGlance = new \stdClass();
    if ($node->field_degree_detail_offered_by && $node->field_degree_detail_offered_by->title && $node->field_degree_detail_offered_by->uri) {
      $atAGlance->offeredBy->text = $node->field_degree_detail_offered_by->title;
      $link = Url::fromUri($node->field_degree_detail_offered_by->uri);
      $atAGlance->offeredBy->url = $link->toString();
    }

    $locations = [];
    foreach ($node->field_degree_detail_locations as $location_link) {
      $itemLink = new \stdClass();
      $itemLink->text = $location_link->title;
      $link = Url::fromUri($location_link->uri);
      $itemLink->url = $link->toString();
      $locations[] = $itemLink;
    }
    if (!empty($locations)) {
      $atAGlance->locations = $locations;
    }
    if ($node->field_degree_detail_first_math->value) {
      $atAGlance->firstRequirementMathCourse = $node->field_degree_detail_first_math->value;
    }
    if ($node->field_degree_detail_math_intense->value) {
      $atAGlance->mathIntensity = $node->field_degree_detail_math_intense->value;
    }
    if ($node->field_degree_detail_time_commit->value) {
      $atAGlance->timeCommitment = $node->field_degree_detail_time_commit->value;
    }
    $props['atAGlance'] = $atAGlance;

    //nextSteps
    $nextSteps = new \stdClass();
    $cards = [];
    foreach ($node->field_degree_detail_nxtsteps  as $paragraph_ref) {
      $card = \Drupal::service('asu_degree_rfi.helper_functions')->getNxtStepsContent($paragraph_ref->entity);
      if (!empty((array)$card)) {
        $cards[] = $card;
      }
    }
    if (!empty($cards)) {
      $nextSteps->cards = $cards;
      $props['nextSteps'] = $nextSteps;
    }

    //careerOutlook
    $careerOutlook = new \stdClass();
    $image = \Drupal::service('asu_degree_rfi.helper_functions')->getImageFieldValue($node->field_degree_detail_outlook_img);
    if (!empty((array)$image)) {
      $careerOutlook->image = $image;
      $props['careerOutlook'] = $careerOutlook;
    }

    //globalOpportunity
    $globalOpportunity = new \stdClass();
    if ($node->field_deg_dtl_hide_global_opp->value) {
      $globalOpportunity->hide = (bool) $node->field_deg_dtl_hide_global_opp->value;
    }
    $image = \Drupal::service('asu_degree_rfi.helper_functions')->getImageFieldValue($node->field_degree_detail_global_opp);
    if (!empty((array)$image)) {
      $globalOpportunity->image = $image;
    }
    $props['globalOpportunity'] = $globalOpportunity;


    //attendOnline
    $attendOnline = new \stdClass();
    if ($node->field_deg_dtl_hide_attend_online->value) {
      $attendOnline->hide = (bool) $node->field_deg_dtl_hide_attend_online->value;
    }
    $image = \Drupal::service('asu_degree_rfi.helper_functions')->getImageFieldValue($node->field_deg_dtl_attend_online_img);
    if (!empty((array)$image)) {
      $attendOnline->image = $image;
    }
    $props['attendOnline'] = $attendOnline;

    //Anchor menu
    $anchor_menu_fields = [
      'affordingCollege' => 'field_deg_dtl_anchor_afford_col',
      'applicationRequirements' => 'field_deg_dtl_anchor_app_reqs',
      'atAGlance' => 'field_deg_dtl_anchor_glance',
      'attendOnline' => 'field_deg_dtl_anchor_online',
      'careerOutlook' => 'field_deg_dtl_anchor_outlook',
      'changeMajorRequirements' => 'field_deg_dtl_anchor_change_mjr',
      'customizeYourCollegeExperience' => 'field_deg_dtl_anchor_customize',
      'globalOpportunity' => 'field_deg_dtl_anchor_global_opp',
      'exampleCareers' => 'field_deg_dtl_anchor_example_crs',
      'programContactInfo' => 'field_deg_dtl_anchor_pgm_contact',
      'nextSteps' => 'field_deg_dtl_anchor_next_steps',
    ];
    $anchorMenu  = new \stdClass();
    foreach ($anchor_menu_fields as $key => $field) {
      if ($node->{$field}) {
        $anchorMenu->{$key} = (bool) $node->{$field}->value;
      }
    }
    $externalAnchors = [];
    if ($node->field_deg_dtl_anchor_addl_anchor) {
      $external_links = $node->field_deg_dtl_anchor_addl_anchor->getValue();
      foreach ($external_links as $link) {
        $link_url = Url::fromUri($link['uri']);
        $externalAnchors[] = (object) [
          'targetIdName' => $link_url->toString(),
          'text' => $link['title'],
        ];
      }
    }
    if (!empty($externalAnchors)) {
      $anchorMenu->externalAnchors = $externalAnchors;
    }

    $props['anchorMenu'] = $anchorMenu;

    $hide_fields = [
      'affordingCollege' => 'field_deg_dtl_hide_affording',
      'applicationRequirements' => 'field_deg_dtl_hide_app_reqs',
      'changeMajorRequirements' => 'field_deg_dtl_hide_chg_major',
      'exampleCareers' => 'field_deg_dtl_hide_example_crs',
      'flexibleDegreeOptions' => 'field_deg_dtl_hide_flex_options',
    ];
    foreach ($hide_fields as $key => $hide_field) {
      if ($node->{$hide_field}) {
        $props[$key] = (object) [
          'hide' => (bool) $node->{$hide_field}->value,
        ];
      }
    }

    //programContactInfo
    $programContactInfo = new \stdClass();
    if ($node->field_degree_detail_pgm_dept_url->value) {
      $programContactInfo->departmentUrl = $node->field_degree_detail_pgm_dept_url->value;
    }
    if ($node->field_degree_detail_email_link->value) {
      $programContactInfo->emailUrl = $node->field_degree_detail_email_link->value;
    }
    $props['programContactInfo'] = $programContactInfo;

    $block_output = [];

    $block_output['#markup'] =
      $this->t('
      <!-- ProgramDetailPage component will be initialized in this container. -->
      <div id="degreeDetailPageContainer"></div>
      ');
    // Attach components and helper js registered in asu_degree_rfi.libraries.yml
    $block_output['#attached']['library'][] = 'asu_degree_rfi/program-detail-page';
    //Pass block configs to javascript. Gets taken up in js/asu_brand.header.js
    $block_output['#attached']['drupalSettings']['asu_degree_rfi']['program_detail_page'] = $props;
    return $block_output;
  }
}