<?php

use \Glavweb\GoogleCloudPrint\GoogleCloudPrint as GoogleCloudPrint;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Printing_Google_CLoud_Print extends GoogleCloudPrint {

	public function search( $query = '', $postFields = array(), $headers = array())
	{
		$headers = array_merge($headers, array(
			"GData-Version: 3.0",
		));

		$response = $this->makeHttpCall(self::URL_SEARCH . $query, $postFields, $headers);
		return json_decode($response);
	}

	private function makeHttpCall($url, $postFields = array(), $headers = array())
	{
		$headers = array_merge($headers, array(
			"Authorization: Bearer " . $this->getAccessToken()
		));

		$curl = curl_init($url);

		if (!empty($postFields)) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		// Execute the curl and return response
		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}

}
