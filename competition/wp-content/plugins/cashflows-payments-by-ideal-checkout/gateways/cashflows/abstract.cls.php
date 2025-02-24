<?php

	class iccf_abstract_class
	{
		private static $sPluginName = 'ic-cashflows-for-woo';
		private static $sPrefix = 'cashflows';
		public static $oRefund = null;

		private static $aPaymentMethods = array
		(
			'cards'
		);

		public function __construct()
		{

		}		

		// iccf_abstract_class::getApiKey()
		public static function getApiKey()
		{
			if(self::getDeveloperMode())
			{
				$sPasswordKey = get_option('iccf_int_api_key');
			}
			else
			{
				$sPasswordKey = get_option('iccf_prod_api_key');
			}

			if(!empty($sPasswordKey))
			{
				return $sPasswordKey;
			}

			return '';
		}

		// iccf_abstract_class::getConfigurationId()
		public static function getConfigurationId()
		{
			if(self::getDeveloperMode())
			{
				$sConfigurationId = get_option('iccf_int_configuration_id');
			}
			else
			{
				$sConfigurationId = get_option('iccf_prod_configuration_id');
			}

			if(!empty($sConfigurationId))
			{
				return $sConfigurationId;
			}

			return '';
		}

		// iccf_abstract_class::getDeveloperMode()
		public static function getDeveloperMode()
		{
			$sDeveloperMode = get_option('iccf_develop');

			if(strcmp($sDeveloperMode, 'yes') === 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		// iccf_abstract_class::getAdditionalDataOption()
		public static function getAdditionalDataOption()
		{
			$bAdditionalData = get_option('iccf_additional_data');

			if(strcmp($bAdditionalData, 'yes') === 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		// iccf_abstract_class::getGoogleAnalyticsOption()
		public static function getGoogleAnalyticsOption()
		{
			$bGoogleAnalytics = get_option('iccf_google_ananlytics');

			if(strcmp($bGoogleAnalytics, 'yes') === 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		// iccf_abstract_class::getMetadataCustomerReference()
		public static function getMetadataCustomerReference($iUserId)
		{

			if(self::getDeveloperMode())
			{
				return get_user_meta($iUserId, 'cashflows_int_customerReference', true);
			}
			else
			{
				return get_user_meta($iUserId, 'cashflows_prod_customerReference', true);
			}

			return '';
		}

		// iccf_abstract_class::getStoreCustomerData()
		public static function getStoreCustomerData()
		{
			$bStoreCustomerData = get_option('iccf_store_customer_data');

			if(strcmp($bStoreCustomerData, 'yes') === 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		// iccf_abstract_class::getPrefix()
		public static function getPrefix()
		{
			return self::$sPrefix . '_';
		}

		// iccf_abstract_class::getPluginName()
		public static function getPluginName()
		{
			return self::$sPluginName;
		}

		// iccf_abstract_class::getPaymentMethods()
		public static function getPaymentMethods()
		{
			$aPaymentMethods = array();

			if(sizeof(self::$aPaymentMethods))
			{
				foreach(self::$aPaymentMethods as $sPaymentMethod)
				{
					$aPaymentMethods[] = iccf_abstract_class::getPrefix() . $sPaymentMethod;
				}
			}

			if(sizeof($aPaymentMethods))
			{
				return $aPaymentMethods;
			}
			else
			{
				error_log('Something went wrong: FILE: ' . __FILE__ . ' and LINE: ' . __LINE__ . ';');
				return false;
			}
		}

		// iccf_abstract_class::getRefundId()
		public static function getRefund()
		{
			if(self::$oRefund)
			{
				return self::$oRefund;
			}
			else
			{
				return null;
			}
		}

		// iccf_abstract_class::setMetadataCustomerReference()
		public static function setMetadataCustomerReference($iUserId, $sCustomerReference)
		{
			if(self::getDeveloperMode())
			{
				return update_user_meta($iUserId, 'cashflows_int_customerReference', $sCustomerReference);
			}
			else
			{
				return update_user_meta($iUserId, 'cashflows_prod_customerReference', $sCustomerReference);
			}
		}

		// iccf_abstract_class::setRefundId()
		public static function setRefund($oRefund)
		{
			self::$oRefund = $oRefund;
		}
	}


?>
