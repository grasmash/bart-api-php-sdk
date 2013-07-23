<?php

/**
 * @file
 * Provides SDK for connecting to BART API resources.
 */
class Bart {
    private $apiHost = "http://api.bart.gov/api";
    private $apiVersion = null;
    private $apiKey = null;

    /**
    * Class Constructor
    */
    public function __construct($key){
        $this->apiKey = $key;
        $urlLogin = "bsa.aspx";

        $getVals = array(
          'cmd' => 'ver',
          'key' => $key,
        );

        $response = $this->runCurl($urlLogin, $getVals);

        if (!empty($response->message->error)){
            return "Connection error";
        }
        else {
          $this->apiVersion = $response->apiVersion;
          return $this->apiVersion;
        }
    }

    /**
    * cURL request
    *
    * General cURL request function for GET and POST
    * @link URL
    * @param string $url URL to be requested
    * @param string $postVals NVP string to be send with POST request
    */
    private function runCurl($url, $getVals = array(), $postVals = null) {

      // Prepend apiHost URL.
      $url = $this->apiHost . '/' . $url;

      // Add $_GET params.
      $getVals = array_merge($getVals, array('key' => $this->apiKey));
      $query_string = $this->buildQueryString($getVals);
      $url .= (strpos($url, '?') !== FALSE ? '&' : '?') . $query_string;

      $ch = curl_init($url);

      $options = array(
          CURLOPT_RETURNTRANSFER => true,
          // CURLOPT_COOKIE => "key=value",
          CURLOPT_TIMEOUT => 3
      );

      if ($postVals != null){
          $options[CURLOPT_POSTFIELDS] = $postVals;
          $options[CURLOPT_CUSTOMREQUEST] = "POST";
      }

      curl_setopt_array($ch, $options);

      $response = simplexml_load_string(curl_exec($ch));
      $response = json_decode(json_encode($response));

      curl_close($ch);

      return $response;
    }

    /**
     * Parses an array into a valid, rawurlencoded query string.
     *
     * This differs from http_build_query() as we need to rawurlencode() (instead of
     * urlencode()) all query parameters.
     *
     * @param $query
     *   The query parameter array to be processed, e.g. $_GET.
     * @param $parent
     *   Internal use only. Used to build the $query array key for nested items.
     *
     * @return
     *   A rawurlencoded string which can be used as or appended to the URL query
     *   string.
     *
     * @see drupal_http_build_query().
     */
    public function buildQueryString(array $query, $parent = '') {
      $params = array();

      foreach ($query as $key => $value) {
        $key = ($parent ? $parent . '[' . rawurlencode($key) . ']' : rawurlencode($key));

        // Recurse into children.
        if (is_array($value)) {
          $params[] = $this->buildQueryString($value, $key);
        }
        // If a query parameter value is NULL, only append its key.
        elseif (!isset($value)) {
          $params[] = $key;
        }
        else {
          // For better readability of paths in query strings, we decode slashes.
          $params[] = $key . '=' . str_replace('%2F', '/', rawurlencode($value));
        }
      }

      return implode('&', $params);
    }

    /**
     * [getRealTimeEstimate description]
     * @param  string $station [description]
     * @return [type]          [description]
     */
    public function getRealTimeEstimate($station = 'all') {
      $resource = 'etd.aspx';

      $getVals = array(
        'cmd' => 'etd',
        'orig' => $station,
      );

      return $this->runCurl($resource, $getVals);
    }
}
