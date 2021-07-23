<?php

namespace Drupal\asu_degree_rfi;
use Drupal\Core\Url;

/**
 * Class AsuDegreeRfiHelperFunctions.php.
 */
class AsuDegreeRfiHelperFunctions {

  public function getImageFieldValue($field) {
    $image = new \stdClass();
    if ($field->target_id && $field->entity->field_media_image->target_id) {
      $image->url = file_create_url($field->entity->field_media_image->entity->getFileUri());
      $image->altText = $field->entity->field_media_image->alt;
    }
    return $image;
  }

  public function getVideoFieldValue($field) {
    $video = new \stdClass();
    if ($field->target_id && $field->entity->field_media_video_file->target_id) {
      $video->url = file_create_url($field->entity->field_media_video_file->entity->getFileUri());
      $video->altText = $field->entity->field_media_video_file->name;
    }
    return $video;
  }

  public function getNxtStepsContent($paragraph) {
    if (empty($paragraph)) {
      return;
    }
    $card = new \stdClass();

    if (isset($paragraph->field_degree_nxtsteps_card_icon) && $paragraph->field_degree_nxtsteps_card_icon->icon_name) {
      $icon_name = $paragraph->field_degree_nxtsteps_card_icon->icon_name;
      $icon_style = $paragraph->field_degree_nxtsteps_card_icon->style;
      $card->icon = [$icon_style, $icon_name];
    }
    if ($paragraph->field_degree_nxtsteps_card_title->value) {
      $card->title = $paragraph->field_degree_nxtsteps_card_title->value;
    }
    if ($paragraph->field_degree_nxtstep_card_contnt->value) {
      $card->content = $paragraph->field_degree_nxtstep_card_contnt->value;
    }
    $buttonLink = new \stdClass();
    if ($paragraph->field_degree_nxtsteps_card_btn && $paragraph->field_degree_nxtsteps_card_btn->title && $paragraph->field_degree_nxtsteps_card_btn->uri) {
      $buttonLink->label = $paragraph->field_degree_nxtsteps_card_btn->title;
      $link = Url::fromUri($paragraph->field_degree_nxtsteps_card_btn->uri);
      $buttonLink->href = $link->toString();
      if ($paragraph->field_degree_nxtsteps_btn_color->value) {
        $buttonLink->color = $paragraph->field_degree_nxtsteps_btn_color->value;
      }
    }

    if (!empty((array)$buttonLink)) {
      $card->buttonLink = $buttonLink;
    }
    return $card;
  }

}