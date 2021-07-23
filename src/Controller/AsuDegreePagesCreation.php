<?php

namespace Drupal\asu_degree_rfi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Controller for the RFI component proxy to the Submit Handler Lambda.
 */
class AsuDegreePagesCreation extends ControllerBase {

  public function load() {
    $path = \Drupal::service('path.current')->getPath();
    $pattern_ulr = '/^\/(bachelors\-degrees|undergraduate\-certificates|graduate\-certificates|masters\-degrees-phds)\/majorinfo\/[A-Z]+\/(undergrad|graduate)\/(true|false)\/[0-9]+$/';

    if (preg_match($pattern_ulr, $path)) {
      $split_path = explode('/', $path);
      $node = Node::create(['type' => 'degree_detail_page']);
      $node->set('title', $split_path[3]);
      $node->set('field_degree_detail_acadplancode', $split_path[3]);
      $node->status = 1;
      $node->enforceIsNew();
      $node->set('path', $path);
      $node->save();
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()])->toString();
      $response = new RedirectResponse(URL::fromUserInput($url)->toString());
      $response->send();
    }
    else {
      return['#markup' => $this->t('The requested page could not be found.')];
    }
  }
}
