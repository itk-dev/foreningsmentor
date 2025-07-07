<?php

namespace Drupal\address_dawa;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Utility\Error;

/**
 * Defines the interface for DAWA address.
 */
class AddressDawa implements AddressDawaInterface {

  /**
   * Guzzle client service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactoroy;

  /**
   * AddressDawa service constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   Guzzle http client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger channel factory.
   */
  public function __construct(ClientInterface $client, LoggerChannelFactoryInterface $logger_factory) {
    $this->client = $client;
    $this->loggerFactoroy = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchAddress(array $options, string $address_type = '') {
    try {
      $response = $this->client->request('GET',
        static::getDawaApiUrl($address_type), [
          'timeout' => 10,
          'query' => $options,
        ]
      );
    }
    catch (RequestException $e) {
      $logger = \Drupal::logger('address_dawa');
      Error::logException($logger, $e);
      return [];
    }
    $result = json_decode($response->getBody()->getContents());
    if ($response->getStatusCode() === 200) {
      return $result;
    }
    $this->loggerFactoroy->get('address_dawa')->error('Something went wrong.');
    return [];
  }

  /**
   * Get DAWA API Url for address type.
   *
   * @param string $address_type
   *   DAWA address type.
   *
   * @return string
   *   DAWA API Url.
   *
   * @see \Drupal\Tests\address_dawa\FunctionalJavascript\AddressDawaFieldTest
   */
  public static function getDawaApiUrl(string $address_type = '') {
    // Automated test in drupal.org environment fails if https.
    if ($address_type == 'adresse') {
      return 'http://dawa.aws.dk/adresser';
    }

    if ($address_type == 'adgangsadresse') {
      return 'http://dawa.aws.dk/adgangsadresser';
    }

    return 'http://dawa.aws.dk/autocomplete';
  }

}
