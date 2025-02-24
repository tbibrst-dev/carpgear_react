<?php

	class CashflowsPayment
	{
		private $sPrimaryPassword = '';
		private $iConfigurationId = 0;
		private $bTestMode = false;
		private $sCachePath = false;
		private $sPaymentMethod = false;
		private $sLocale = 'en_GB';
		private $aAllowedLocales = array('en-AU', 'en-BZ', 'en-CA', 'en-GB', 'en-IE', 'en-IN', 'en-JM', 'en-MY', 'en-NZ', 'en-PH', 'en-SG', 'en-TT', 'en-US', 'en-ZA', 'en-ZW', 'es-AR', 'es-BO', 'es-CL', 'es-CO', 'es-CR', 'es-DO', 'es-EC', 'es-ES', 'es-GT', 'es-HN', 'es-MX', 'es-NI', 'es-PA', 'es-PE', 'es-PR', 'es-PY', 'es-SV', 'es-US', 'es-UY', 'es-VE', 'nl_NL', 'nl_BE', 'de_DE', 'en_GB', 'en_US');

		private $aBillingData = array();
		private $aBillingIdentity = array();
		private $aShippingData = array();
		private $aOrderLines = array();

		private $sReturnUrl = false;
		private $sReturnFailureUrl = false;
		private $sReportUrl = false;

		private $sTransactionUrl = false;

		private $sOrderId = false;
		private $fAmount = false;
		private $fRefundAmount = false;
		private $sCurrencyCode = 'GBP';
		private $aAllowedCurrencies = array('AUD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'HKD', 'JPY', 'NZD', 'NOK','SEK', 'SGD', 'USD', 'ZAR');

		private $sDescription = '';
		private $sDescriptor = '';
		private $sCookie = false;
		private $sCustomerReference = false;

		private $sJsonResponse = '';
		private $iPaymentJobReference = false;
		private $iPaymentReference = false;
		private $bStoreCustomerInformation = false;

		private $aTransaction = array();
		private $aTransactionResults = false;
		private $aPaymentJob = array();
		private $aErrors = array();


		public function __construct($sPrimaryPassword = '', $iConfigurationId = 0)
		{
			$this->sPrimaryPassword = $sPrimaryPassword;
			$this->iConfigurationId = $iConfigurationId;
		}


		public function setPrimaryPassword($sPrimaryPassword)
		{
			$this->sPrimaryPassword = $sPrimaryPassword;
			return true;
		}

		public function setConfigurationId($iConfigurationId)
		{
			$this->iConfigurationId = $iConfigurationId;
			return true;
		}


		public static function convertAmountToString($fAmount)
		{
			return (string)round($fAmount, 3, PHP_ROUND_HALF_DOWN);
		}

		public function setAnalyticsCookie($sCookie = false)
		{
			$this->sCookie = $sCookie;
		}

		private function calculateHash($sBaseString, $sPasswordToHashWith)
		{
			// Function from CASHFLOWS
			if(empty($sBaseString))
			{
				$sBaseString = '';
			}

			$sComputedHash = hash('sha512', $sBaseString . $sPasswordToHashWith, true);

			return strtoupper(bin2hex($sComputedHash));
		}

		public function setCachePath($sPath = false)
		{
			// Should point to directory where cache is strored
			$this->sCachePath = $sPath;
		}

		public function setCurrencyCode($sCurrencyCode = false)
		{
			if(is_bool($sCurrencyCode))
			{
				$this->sCurrencyCode = false;
				return true;
			}
			elseif(is_string($sCurrencyCode))
			{
				$sCurrencyCode = strtoupper(substr($sCurrencyCode, 0, 3));

				if(in_array($sCurrencyCode, $this->aAllowedCurrencies))
				{
					$this->sCurrencyCode = $sCurrencyCode;
					return true;
				}
			}

			return false;
		}

		public function setCustomerReference($sCustomerReference)
		{
			$this->sCustomerReference = $sCustomerReference;
		}

		public function setLocale($sLocale = false)
		{
			if(is_bool($sLocale))
			{
				$this->sLocale = false;
				return true;
			}
			elseif(is_string($sLocale))
			{
				$sLocale = substr($sLocale, 0, 5);

				if(strpos($sLocale, '-'))
				{
					$sLocale = str_replace('-', '_', $sLocale);
				}

				if(preg_match('/([a-z]{2,2})_([A-Z]{2,2})/', $sLocale))
				{
					if(in_array($sLocale, $this->aAllowedLocales))
					{
						$this->sLocale = $sLocale;
						return true;
					}
				}
				else
				{
					$this->sLocale = 'en_GB';
					return true;
				}
			}

			return false;
		}

		public function setOrderDescription($sOrderNumber, $sDescription = false)
		{
			$this->sDescription = $sDescription;

			if(empty($this->sDescription))
			{
				$this->sDescription = 'Webshop bestelling ' . $sOrderNumber;
			}

			return true;
		}

		public function setOrderAmount($fAmount)
		{
			$this->fAmount = $fAmount;
		}

		public function setRefundAmount($fAmount)
		{
			$this->fRefundAmount = $fAmount;
		}

		public function setRefundDescriptor($sMessage)
		{
			$this->sDescriptor = $sMessage;
		}

		public function setOrderId($sOrderId)
		{
			$this->sOrderId = $sOrderId;
		}

		public function setPaymentMethod($sPaymentMethod = false)
		{
			if(is_bool($sPaymentMethod))
			{
				$this->sPaymentMethod = false;
				return true;
			}
			elseif(is_string($sPaymentMethod))
			{
				$sPaymentMethod = strtolower($sPaymentMethod);

				if(in_array($sPaymentMethod, array('ideal', 'creditcard', 'card', 'paypal')))
				{
					$this->sPaymentMethod = ucfirst($sPaymentMethod);
					return true;
				}
			}

			return false;
		}

		public function setPaymentJobReference($iPaymentJobReference = false)
		{
			$this->iPaymentJobReference = $iPaymentJobReference;
		}

		public function setPaymentReference($iPaymentReference = false)
		{
			$this->iPaymentReference = $iPaymentReference;
		}

		public function setReturnUrl($sReturnUrl = false)
		{
			$this->sReturnUrl = $sReturnUrl;
		}

		public function setReturnFailureUrl($sReturnFailureUrl = false)
		{
			$this->sReturnFailureUrl = $sReturnFailureUrl;
		}

		public function setReportUrl($sReportUrl = false)
		{
			$this->sReportUrl = $sReportUrl;
		}

		public function setResponse($sJsonResponse = '')
		{
			$this->sJsonResponse = $sJsonResponse;
		}

		public function setTestmode($bEnabled = false)
		{
			return ($this->bTestMode = $bEnabled);
		}

		public function setTransactionUrl($sTransactionUrl = false)
		{
			$this->sTransactionUrl = $sTransactionUrl;
		}

		public function setBillingData($aBillingData)
		{
			$this->aBillingData = $aBillingData;
		}

		public function setBillingIdentity($aBillingIdentity)
		{
			$this->aBillingIdentity = $aBillingIdentity;
		}

		public function setShippingData($aShippingData)
		{
			$this->aShippingData = $aShippingData;
		}

		public function setOrderLines($aOrderLines)
		{
			$this->aOrderLines = $aOrderLines;
		}

		public function setStoreCustomerInformation($bStoreCustomerInformation)
		{
			$this->bStoreCustomerInformation = $bStoreCustomerInformation;
			return true;
		}

		private function getHeaders($sPostData = '')
		{
			$aHeaders = array(
				'hash' => $this->calculateHash($this->sPrimaryPassword, $sPostData),
				'configurationId' => $this->iConfigurationId,
				'Content-Type' => 'application/json; charset=utf-8'
			);

			return $aHeaders;
		}

		public function createRequestUrl($sRequest = '')
		{
			if($this->bTestMode)
			{
				$sUrl = 'https://gateway-int.cashflows.com';
			}
			else
			{
				$sUrl = 'https://gateway.cashflows.com';
			}

			return $sUrl . $sRequest;
		}

		public function getJsonResponse()
		{
			return $this->sJsonResponse;
		}

		public function getTransaction()
		{
			if(empty($this->sPrimaryPassword))
			{
				$this->aTransaction = array('error' => array('message' => 'No API key found.'));
				return false;
			}
			elseif(empty($this->sOrderId))
			{
				$this->aTransaction = array('error' => array('message' => 'No order ID found.'));
				return false;
			}
			elseif(empty($this->fAmount))
			{
				$this->aTransaction = array('error' => array('message' => 'No amount found.'));
				return false;
			}
			elseif(empty($this->sReturnUrl))
			{
				$this->aTransaction = array('error' => array('message' => 'No return URL found.'));
				return false;
			}
			elseif(empty($this->sReturnFailureUrl))
			{
				$this->aTransaction = array('error' => array('message' => 'No return failure URL found.'));
				return false;
			}

			$aRequest = array();
			$aRequest['locale'] = $this->sLocale;

			$aOrder = array('orderNumber' => $this->sOrderId, 'note' => $this->sDescription);

			if(!empty($this->sCustomerReference))
			{
				$aOrder['customerReference'] = $this->sCustomerReference;
			}

			if(!empty($this->aBillingData))
			{
				$aOrder['billingAddress'] = $this->aBillingData;
			}

			if(!empty($this->aBillingIdentity))
			{
				$aOrder['billingIdentity'] = $this->aBillingIdentity;
			}

			if(!empty($this->aBillingData))
			{
				$aOrder['billingAddress'] = $this->aBillingData;
			}

			if(!empty($this->aShippingData))
			{
				$aOrder['shippingAddress'] = $this->aShippingData;
			}

			if(!empty($this->aOrderLines))
			{
				$aOrder['orderLines'] = $this->aOrderLines;
			}

			$aRequest['order'] = $aOrder;
			$aRequest['amountToCollect'] = self::convertAmountToString($this->fAmount);
			$aRequest['currency'] = $this->sCurrencyCode;
			$aRequest['paymentMethodsToUse'] = array($this->sPaymentMethod);

			$aParameters = array
			(
				'returnUrlSuccess' => $this->sReturnUrl,
				'returnUrlCancelled' => $this->sReturnFailureUrl,
				'returnUrlFailed' => $this->sReturnFailureUrl,
				'webhookUrl' => $this->sReportUrl
			);

			if(!empty($this->sCookie))
			{
				$aParameters['GoogleAnalyticsClientId'] = $this->sCookie;
			}

			$aRequest['parameters'] = $aParameters;

			$aRequest['options'] = array();

			if(!empty($this->bStoreCustomerInformation))
			{
				$aRequest['options'][] = 'StoreCustomerInformation';
			}

			$sApiUrl = $this->createRequestUrl('/api/gateway/payment-jobs');

			$sPostData = json_encode($aRequest);

			$sResponse = idealcheckout_doHttpRequest($sApiUrl, $sPostData, true, 30, false, $this->getHeaders($sPostData));

			if(!empty($sResponse))
			{
				$this->aTransaction = json_decode($sResponse, true);

				$this->setResponse($sResponse);

				if($this->aTransaction)
				{
					if(isset($this->aTransaction['data']) && isset($this->aTransaction['data']['reference']) && isset($this->aTransaction['links']) && isset($this->aTransaction['links']['action']) && isset($this->aTransaction['links']['action']['url']))
					{
                        $this->setPaymentJobReference($this->aTransaction['data']['reference']);
						$this->setTransactionUrl($this->aTransaction['links']['action']['url']);
						return true;
					}
					else
					{
						if(isset($this->aTransaction['errorReport']) && isset($this->aTransaction['errorReport']['errors']))
						{
							foreach($this->aTransaction['errorReport']['errors'] as $aError)
							{
								$this->aTransaction = array('error' => array('code' => $aError['code'], 'message' => $aError['message']));
							}
						}
						else
						{
							return $sResponse;
						}
					}
				}
				else
				{
					$this->aTransaction = array('error' => array('message' => 'Cannot decode JSON response (See logs).'));
				}
			}
			else
			{
				$this->aTransaction = array('error' => array('message' => 'No response received from Cashflows (See logs).'));
			}

			return false;
		}

		public function getTransactionUrl()
		{
			return $this->sTransactionUrl;
		}
		public function getTransactionId()
		{
			return $this->iPaymentJobReference;
		}

		public function getUsedPaymentMethod()
		{
			if(!empty($this->aPaymentJob))
			{
				if(isset($this->aPaymentJob['data']['payments']) && isset($this->aPaymentJob['data']['payments'][count($this->aPaymentJob['data']['payments']) - 1]) && isset($this->aPaymentJob['data']['payments'][count($this->aPaymentJob['data']['payments']) - 1]['paymentMethods']))
				{
					return strtolower($this->aPaymentJob['data']['payments'][count($this->aPaymentJob['data']['payments']) - 1]['paymentMethods'][0]);
				}
			}
		}

		public function getError()
		{
			if(!empty($this->aTransaction['error']))
			{
				return $this->aTransaction['error'];
			}

			return false;
		}

		public function getPaymentJob()
		{
			// Object empty, create the new one.
			if(empty($this->aPaymentJob))
			{
				$this->aPaymentJob = $this->doRetrievePaymentJob();
			}

			return $this->aPaymentJob;
		}

		public function getStatus($iPaymentJobReference = null)
		{
			$this->setPaymentJobReference($iPaymentJobReference);

			if($this->getPaymentJob() && sizeof($this->aPaymentJob))
			{
				if (isset($this->aPaymentJob['data']['paymentStatus']))
				{
					$sCashflowsStatus = $this->aPaymentJob['data']['paymentStatus'];

					if(!empty($sCashflowsStatus))
					{
						if(in_array($sCashflowsStatus, array('Paid')))
						{
							return 'SUCCESS';
						}
						elseif(in_array($sCashflowsStatus, array('Cancelled')))
						{
							return 'CANCELLED';
						}
						elseif(in_array($sCashflowsStatus, array('Failed', 'Rejected', 'Expired')))
						{
							return 'FAILURE';
						}
						else
						{
							return 'PENDING';
						}
					}
				}
			}

			return '';
		}

		private function doRetrievePaymentJob()
		{
			if(!empty($this->iPaymentJobReference))
			{
				$sApiUrl = $this->createRequestUrl('/api/gateway/payment-jobs/' . $this->iPaymentJobReference);

				$sResponse = idealcheckout_doHttpRequest($sApiUrl, false, true, 30, false, $this->getHeaders());

				if(!empty($sResponse))
				{
					$this->setResponse($sResponse);
					$aResponse = json_decode($sResponse, true);

					return $aResponse;
				}
			}

			return '';
		}

		public function doRetrievePaymentMethods()
		{
			$sApiUrl = $this->createRequestUrl('/api/gateway/supported-payment-methods');

			$sResponse = idealcheckout_doHttpRequest($sApiUrl, false, true, 30, false, $this->getHeaders());

			if(!empty($sResponse))
			{
				$this->setResponse($sResponse);
				$aResponse = json_decode($sResponse, true);

				if(isset($aResponse['data']))
				{
					return $aResponse['data'];
				}
			}

			return '';
		}

		public function doRefund()
		{
			$aRequest = array();

			if(empty($this->iPaymentJobReference))
			{
				return false;
			}

			if(empty($this->iPaymentReference))
			{
				return false;
			}

			if($this->fRefundAmount > 0.00)
			{
				$aRequest['amountToRefund'] = self::convertAmountToString($this->fRefundAmount);
			}
			else
			{
				return false;
			}

			if(empty($this->sDescriptor))
			{
				$aRequest['refundNumber'] = 'Refunded transaction for: ' . $this->iPaymentJobReference;
			}
			else
			{
				$aRequest['refundNumber'] = $this->sDescriptor;
			}

			$sApiUrl = $this->createRequestUrl('/api/gateway/payment-jobs/' . $this->iPaymentJobReference . '/payments/' . $this->iPaymentReference . '/refunds');

			$sPostData = json_encode($aRequest);

			$sResponse = idealcheckout_doHttpRequest($sApiUrl, $sPostData, true, 30, false, $this->getHeaders($sPostData));

			if($sResponse)
			{
				$this->setResponse($sResponse);
				$aResponse = json_decode($sResponse, true);

				if(isset($aResponse['data']))
				{
					return true;
				}
			}

			return false;
		}
	}

?>
