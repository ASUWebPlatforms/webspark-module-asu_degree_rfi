<?php

namespace Drupal\asu_degree_rfi;

use Drupal\Component\Serialization\Json;
use \GuzzleHttp\Exception\RequestException;

class AsuDegreeRfiDataPotluckClient {

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * AsuDegreeRfiDataPotluckClient constructor.
   *
   * @param $http_client_factory \Drupal\Core\Http\ClientFactory
   */
  public function __construct($http_client_factory) {
    $this->client = $http_client_factory->fromOptions([
      'base_uri' => 'https://api.myasuplat-dpl.asu.edu/',
    ]);
  }

  // Services reference:
  // Docs: https://api.myasuplat-dpl.asu.edu/
  // https://api.myasuplat-dpl.asu.edu/api/codeset/countries
  // https://api.myasuplat-dpl.asu.edu/api/codeset/country/US
  // https://api.myasuplat-dpl.asu.edu/api/codeset/country/CA
  // https://api.myasuplat-dpl.asu.edu/api/codeset/colleges


  // TODO Improve methods with a layer of abstraction so we're not repeating
  // ourselves as much.


  /**
   * Get countries (options-ready).
   *
   * @return array
   */
  public function countries() {
    $country_data = [];
    try {
      // Do service call and build out the return data.
      $response = $this->client->get('api/codeset/countries');
      $json_data =  Json::decode($response->getBody());
      foreach ($json_data as $row) {
        // For options...
        $country_data[$row["countryCodeTwoChar"]] = $row["description"];
      }
    } catch (RequestException $exception) {
      $msg = t("Failed to retrieve country field data: :code - :msg", [":code" => $exception->getCode(), ":msg" => $exception->getMessage()]);
      \Drupal::logger('asu_degree_rfi')->error($msg);
      \Drupal::messenger()->addError(t('Please try again in 5 minutes.') . ' ' . $msg);
    }
    return $country_data;
  }


  /**
   * Get US states/provinces (options-ready).
   *
   * @param array $countries
   *
   * @return array
   */
  public function statesProvinces($countries = ['US', 'CA']) {
    $state_province_output = [];
    foreach ($countries as $country) {
      try {
        $response = $this->client->get('api/codeset/country/' . $country);
        $json_data = Json::decode($response->getBody());
        foreach ($json_data['states'] as $row) {
          // Segment by country.
          $state_province_output[$country][$row["stateCode"]] = $row["description"];
        }
      } catch (RequestException $exception) {
        $msg = t("Failed to retrieve state and province field data: :code - :msg", [":code" => $exception->getCode(), ":msg" => $exception->getMessage()]);
        \Drupal::logger('asu_degree_rfi')->error($msg);
        \Drupal::messenger()->addError(t('Please try again in 5 minutes.') . ' ' . $msg);
      }
    }
    return $state_province_output;
  }



  /**
   * Get colleges (options-ready).
   *
   * @return array
   */
  public function colleges() {
    $college_output = [];
    try {
      $response = $this->client->get('api/codeset/colleges');
      $json_data =  Json::decode($response->getBody());

      foreach ($json_data as $row) {
        $college_output[$row["acadOrgCode"]] = $row["description"] . " : " . $row['acadOrgCode'];
      }
    } catch (RequestException $exception) {
      $msg = t("Failed to retrieve college field data: :code - :msg", [":code" => $exception->getCode(), ":msg" => $exception->getMessage()]);
      \Drupal::logger('asu_degree_rfi')->error($msg);
      \Drupal::messenger()->addError(t('Please try again in 5 minutes.') . ' ' . $msg);
    }
    asort($college_output);
    return $college_output;
  }
}
