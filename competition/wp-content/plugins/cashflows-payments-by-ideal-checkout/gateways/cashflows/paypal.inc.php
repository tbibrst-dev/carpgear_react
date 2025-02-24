<?php

	class iccf_paypal extends iccf_abstract
	{
		public function _getPaymentCode()
		{
			return 'paypal';
		}

		public function _getPaymentName()
		{
			return 'Paypal';
		}
	}