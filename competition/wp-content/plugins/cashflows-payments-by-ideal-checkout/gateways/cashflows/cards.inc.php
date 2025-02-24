<?php

	class iccf_cards extends iccf_abstract
	{
		public function _getPaymentCode()
		{
			return 'card';
		}

		public function _getPaymentName()
		{
			return 'Cards';
		}
	}