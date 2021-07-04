<?php

namespace Drupal\asu_degree_rfi\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

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


    // Rally props to pass to JS as drupalSettings.
    $props = [];

    // TODO rally settings here

    $block_output = [];

    // Markup containers where components will initialize.
    $block_output['#markup'] =
      $this->t('
      <!-- ProgramDetailPage component will be initialized in this container. -->
      <div id="TBD-program_detail-page">TODO PROGRAM DETAILS PAGE RENDERS HERE TODO</div>
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
}
