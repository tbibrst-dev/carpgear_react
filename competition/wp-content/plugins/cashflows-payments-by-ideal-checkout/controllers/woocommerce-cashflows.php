<?php

	class IdealcheckoutCashflowsForWooCommerce
	{
		private static $bInitiated = false;
		private static $sFolderPath = '';

		public static function init()
		{
			if(self::$bInitiated)
			{
				return;
			}

			self::$sFolderPath = ICCF_ROOT_PATH . DIRECTORY_SEPARATOR . 'gateways' . DIRECTORY_SEPARATOR . 'cashflows';

			// Load methods
			add_action('woocommerce_payment_gateways', array(__CLASS__, 'loadGateways'));
			add_action('woocommerce_create_refund', array(__CLASS__, 'iccf_refunds'));

			// Load functions via Abstract
			self::addAbstracts();

			self::$bInitiated = true;
		}

		public static function addAbstracts()
		{
            if(is_admin())
            {
                // Load settings
                add_filter('woocommerce_payment_gateways_settings', array(__CLASS__, 'addPspSettings'));
            }
            
			// Set hooks for the return, return failure and webhook call
			add_action('woocommerce_api_iccf_gateway_return', array('iccf_abstract', 'doReturn'));
			add_action('woocommerce_api_iccf_gateway_return_failure', array('iccf_abstract', 'doReturnFailure'));
			add_action('woocommerce_api_iccf_gateway_notify', array('iccf_abstract', 'doNotify'));
		}

        public static function addPspSettings($aSettings)
		{
			$aAddedSettings = array();

			$aAddedSettings[] = array(
				'name' => __('Cashflows', 'ic-cashflows-for-woo'),
				'type' => 'title',
				'desc' => __('The following options are required to use Cashflows', 'ic-cashflows-for-woo'),
			);
			$aAddedSettings[] = array(
				'name' => __('Production - API Key', 'ic-cashflows-for-woo'),
				'type' => 'text',
				'desc' => __('The API Key can be found on the <a href="https://portal.cashflows.com/">Cashflows Portal</a>.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_prod_api_key',
			);
			$aAddedSettings[] = array(
				'name' => __('Production - Configuration ID', 'ic-cashflows-for-woo'),
				'type' => 'text',
				'desc' => __('The configuration ID can be found on the <a href="https://portal.cashflows.com/">Cashflows Portal</a>.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_prod_configuration_id',
			);
			$aAddedSettings[] = array(
				'name' => __('Integration - API Key', 'ic-cashflows-for-woo'),
				'type' => 'text',
				'desc' => __('The API Key can be found on the <a href="https://portal-int.cashflows.com/">Cashflows Portal</a>.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_int_api_key',
			);
			$aAddedSettings[] = array(
				'name' => __('Integration - Configuration ID', 'ic-cashflows-for-woo'),
				'type' => 'text',
				'desc' => __('The configuration ID can be found on the <a href="https://portal-int.cashflows.com/">Cashflows Portal</a>.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_int_configuration_id',
			);
			$aAddedSettings[] = array(
				'name' => __('Integration mode', 'ic-cashflows-for-woo'),
				'type' => 'checkbox',
				'label' => __('Enable the Integration mode', 'ic-cashflows-for-woo'),
				'default' => 'yes',
				'desc' => __('Please make sure you are using the correct credentials.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_develop',
			);
			$aAddedSettings[] = array(
				'name' => __('Google Analytics for Cashflows', 'ic-cashflows-for-woo'),
				'type' => 'checkbox',
				'label' => __('Send the Google Analytics ID to CashFlows.', 'ic-cashflows-for-woo'),
				'default' => 'no',
				'desc' => __('Send the content of the Google Analytics cookie to CashFlows. This can only be send if the Google Analytics is used and loaded.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_google_ananlytics',
			);
			$aAddedSettings[] = array(
				'name' => __('Additional customer data', 'ic-cashflows-for-woo'),
				'type' => 'checkbox',
				'label' => __('Enable to send additional customer data to Cashflows', 'ic-cashflows-for-woo'),
				'default' => 'no',
				'desc' => __('Send customer data to CashFlows<br>Customer data is optional, if you do turn on this option, please note this in the privacy statement.', 'ic-cashflows-for-woo'),
				'id' => 'iccf_additional_data',
			);
			$aAddedSettings[] = array(
				'name' => __('Store Customer data', 'ic-cashflows-for-woo'),
				'type' => 'checkbox',
				'label' => __('If enabled CashFlows will store the Customer Data on their servers', 'ic-cashflows-for-woo'),
				'default' => 'no',
				'desc' => '',
				'id' => 'iccf_store_customer_data',
			);
			$aAddedSettings[] = array(
				'type' => 'sectionend',
			);

			
			$aAlteredSettings = array_merge($aAddedSettings, $aSettings);


			return $aAlteredSettings;
		}

		public static function iccf_refunds($oRefund)
		{
			if(!empty($oRefund))
			{
				require_once(self::$sFolderPath . DIRECTORY_SEPARATOR . 'abstract.cls.php');

				iccf_abstract_class::setRefund($oRefund);
			}
		}

		public static function loadGateways($aDefaultGateways)
		{
			// Load our gateways
			iccf_loadFilesFromFolder(self::$sFolderPath);

			$aPaymentGateways = array(
				'iccf_cards',
			);

			$aDefaultGateways = array_merge($aDefaultGateways, $aPaymentGateways);

			return $aDefaultGateways;
		}
	}

?>