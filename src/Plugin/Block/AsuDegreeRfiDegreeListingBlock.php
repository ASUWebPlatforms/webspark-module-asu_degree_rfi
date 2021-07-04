<?php

namespace Drupal\asu_degree_rfi\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * ASU Degree RFI module Degree listing component block.
 *
 * @Block(
 *   id = "asu_degree_rfi_degree_listing_block",
 *   admin_label = @Translation("Degree listing component"),
 * )
 */
class AsuDegreeRfiDegreeListingBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Define cache tag.
    // Gets invalidated when ... TBD
    return Cache::mergeTags(parent::getCacheTags(), array('degree_listing_block_cache'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Degree component blocks are deployed as 1 block to 1 content type. I.e.
    // the Degree listing block is attached to the Degree listing content type,
    // and similarly for the Degree details page and block. This works and still
    // allows for customization/overrides for degree components because the
    // configurations and override values used as the components' props are
    // defined on the node, but pulled in via the block.

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
      <!-- ListingPage component will be initialized in this container. -->
      <div id="TBD-listing-page">TODO LISTING PAGE RENDERS HERE TODO</div>
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
