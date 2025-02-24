<?php

class iccf_abstract extends WC_Payment_Gateway
{
    private $aAdditionalData = [];

    public function __construct()
    {
        $this->id = $this->getPaymentCode();
        $this->icon = $this->getIcon();
        $this->has_fields = true;
        $this->method_title = $this->getPaymentName().$this->getLabel();
        $this->method_description = sprintf(__('Enable this method to receive transactions with Cashflows', 'ic-cashflows-for-woo').' - '.$this->getPaymentName());

        $this->supports = ['products', 'refunds'];

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');

        add_action('woocommerce_update_options_payment_gateways_'.$this->id, [$this, 'process_admin_options']);
    }

    public function payment_fields()
    {
        $sPaymentDescription = $this->get_option('description');

        echo sanitize_text_field($sPaymentDescription);
    }

    public function process_payment($order_id)
    {
        return $this->doStart($order_id);
    }

    public function process_refund($order_id, $amount = null, $reason = '')
    {
        return $this->doRefund($order_id, $amount, $reason);
    }

    public function init_form_fields()
    {
        $this->form_fields = [];

        $this->form_fields['docs'] = [
            'title' => 'Cashflows',
            'type' => 'title',
            'description' => '<a href="https://gateway-int.cashflows.com/payment-gateway-api/documentation/index.html" target="_blank">'.__('Go to documentation', 'ic-cashflows-for-woo').'</a>.',
            ];

        $this->form_fields['enabled'] = [
                'title' => __('Enable/Disable', 'ic-cashflows-for-woo'),
                'type' => 'checkbox',
                'label' => __('Enable Cashflows', 'ic-cashflows-for-woo').' - '.$this->getPaymentName(),
                'default' => 'no',
            ];

        $this->form_fields['title'] = [
                'title' => __('Title', 'ic-cashflows-for-woo'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'ic-cashflows-for-woo'),
                'default' => $this->getPaymentName(),
                'desc_tip' => true,
            ];

        $this->form_fields['description'] = [
                'title' => __('Customer Message', 'ic-cashflows-for-woo'),
                'type' => 'textarea',
                'default' => __('Pay with', 'ic-cashflows-for-woo').$this->getPaymentName(),
            ];
    }

    public function createAdditionalData($oOrder)
    {
        if (empty($oOrder) && !is_object($oOrder)) {
            return false;
        }

        $aData = $oOrder->get_data();

        /*
        billingAddress
        title, firstName, middleName, lastName, countryIso3166Alpha2, addressLine1, addressLine2, zipCode, city, stateProvince, phoneNumber1, phoneNumber1Type, phoneNumber2, phoneNumber2Type, organisation, department
        */

        $this->aAdditionalData['billingAddress'] = [
            'title' => '',
            'firstName' => $aData['billing']['first_name'],
            'middleName' => '',
            'lastName' => $aData['billing']['last_name'],
            'countryIso3166Alpha2' => $aData['billing']['country'],
            'addressLine1' => $aData['billing']['address_1'],
            'addressLine2' => $aData['billing']['address_2'],
            'zipCode' => $aData['billing']['postcode'],
            'city' => $aData['billing']['city'],
            'stateProvince' => $aData['billing']['state'],
            'phoneNumber1' => intval($aData['billing']['phone']),
            'organisation' => $aData['billing']['company'],
        ];

        /*
        billingIdentity
        debtorId, emailAddress, gender, dateOfBirth, socialSecurityNumber, chamberOfCommerceNumber, vatNumber
        */

        $this->aAdditionalData['billingIdentity'] = [
            'debtorId' => '',
            'emailAddress' => $aData['billing']['email'],
            'gender' => '',
            'dateOfBirth' => '',
            'socialSecurityNumber' => '',
            'chamberOfCommerceNumber' => '',
            'vatNumber' => '',
        ];

        /*
        shippingAddress
        title, firstName, middleName, lastName, countryIso3166Alpha2, addressLine1, addressLine2, zipCode, city, stateProvince, phoneNumber1, phoneNumber1Type, phoneNumber2, phoneNumber2Type, organisation, department
        */

        $this->aAdditionalData['shippingAddress'] = [
            'title' => '',
            'firstName' => (!empty($aData['shipping']['first_name']) ? $aData['shipping']['first_name'] : $aData['billing']['first_name']),
            'middleName' => '',
            'lastName' => (!empty($aData['shipping']['last_name']) ? $aData['shipping']['last_name'] : $aData['billing']['last_name']),
            'countryIso3166Alpha2' => (!empty($aData['shipping']['country']) ? $aData['shipping']['country'] : $aData['billing']['country']),
            'addressLine1' => (!empty($aData['shipping']['address_1']) ? $aData['shipping']['address_1'] : $aData['billing']['address_1']),
            'addressLine2' => (!empty($aData['shipping']['address_2']) ? $aData['shipping']['address_2'] : $aData['billing']['address_2']),
            'zipCode' => (!empty($aData['shipping']['postcode']) ? $aData['shipping']['postcode'] : $aData['billing']['postcode']),
            'city' => (!empty($aData['shipping']['city']) ? $aData['shipping']['city'] : $aData['billing']['city']),
            'stateProvince' => (!empty($aData['shipping']['state']) ? $aData['shipping']['state'] : $aData['billing']['state']),
            'phoneNumber1' => (!empty($aData['shipping']['phone']) ? $aData['shipping']['phone'] : $aData['billing']['phone']),
            'phoneNumber1Type' => '',
            'organisation' => (!empty($aData['shipping']['company']) ? $aData['shipping']['company'] : $aData['billing']['company']),
            'department' => '',
        ];

        /*
        orderLines -> 3d array
        lineNumber, type, skuCode, name, description, quantity, unitPriceExclVat, unitPriceInclVat, vatPercentage, vatPercentageLabel, discountPercentageLabel, totalLineAmount, url
        */

        $this->aAdditionalData['orderLines'] = [];
        $oTax = new WC_Tax();

        $iOrderLine = 0;

        foreach ($aData['line_items'] as $oProduct) {
            // $oStoreProduct = wc_get_product((!empty($oProduct->get_variation_id()) ? $oProduct->get_variation_id() : $oProduct->get_product_id());
            $oStoreProduct = wc_get_product($oProduct->get_product_id());

            $aTaxes = $oTax->get_rates($oStoreProduct->get_tax_class());
            $aRates = array_shift($aTaxes);

            $aRates['rate'] = empty($aRates['rate']) ? 0 : $aRates['rate'];

            $aOrderLine = [
                'lineNumber' => $iOrderLine,
                'type' => (($oStoreProduct->get_virtual() || $oStoreProduct->get_downloadable()) ? 'DigitalItem' : 'PhysicalItem'),
                'skuCode' => $oStoreProduct->get_sku(),
                'name' => $oProduct->get_name(),
                'quantity' => CashflowsPayment::convertAmountToString($oProduct->get_quantity()),
                'unitPriceExclVat' => CashflowsPayment::convertAmountToString(wc_get_price_excluding_tax($oStoreProduct)),
                'unitPriceInclVat' => CashflowsPayment::convertAmountToString(wc_get_price_including_tax($oStoreProduct)),
                'vatPercentage' => CashflowsPayment::convertAmountToString($aRates['rate']),
                'vatPercentageLabel' => empty($aRates['label']) ? '' : $aRates['label'],
                'discountPercentageLabel' => '',
                'totalLineAmount' => CashflowsPayment::convertAmountToString(wc_get_price_including_tax($oStoreProduct) * $oProduct->get_quantity()),
                'url' => get_permalink($oProduct->get_product_id()),
            ];

            $this->aAdditionalData['orderLines'][] = $aOrderLine;
            ++$iOrderLine;
        }

        return true;
    }

    public function getIcon()
    {
        $sGatewayCode = $this->_getPaymentCode();

        $oCashflows = new CashflowsPayment(iccf_abstract_class::getApiKey(), iccf_abstract_class::getConfigurationId());
        $oCashflows->setTestmode(iccf_abstract_class::getDeveloperMode());

        return $oCashflows->createRequestUrl('/assets/payment-method-'.$sGatewayCode.'.svg');
    }

    public function getLabel()
    {
        return ' via Cashflows';
    }

    public function getPaymentCode()
    {
        if ($sPaymentCode = $this->_getPaymentCode()) {
            return iccf_abstract_class::getPrefix().$sPaymentCode;
        } else {
            throw new Exception('Forgot the getPaymentCode method for this payment method?');
        }
    }

    public function _getPaymentCode()
    {
        return '';
    }

    public function getPaymentName()
    {
        if ($sPaymentName = $this->_getPaymentName()) {
            return $sPaymentName;
        } else {
            throw new Exception('Forgot the getPaymentName method for this payment method?');
        }
    }

    public function _getPaymentName()
    {
        return '';
    }

    public function isRedirect()
    {
        return true;
    }

    public function doStart($sOrderId)
    {
        global $woocommerce;

        $oOrder = wc_get_order($sOrderId);

        $oCashflows = new CashflowsPayment(iccf_abstract_class::getApiKey(), iccf_abstract_class::getConfigurationId());
        $oCashflows->setTestmode(iccf_abstract_class::getDeveloperMode());

        if (version_compare($woocommerce->version, '3.0', '>=')) {
            // Get all order related data
            $aOrderData = $oOrder->get_data();

            $sOrderNumber = $aOrderData['number'];
            $sDescription = 'Order '.$sOrderNumber;

            // Order amount in Cents
            $fOrderAmount = round($aOrderData['total'], 2);

            // We support 14 currencies: British Pounds; Euro's; Dollar: Australia, Canada, Hong Kong, New Zealand, Singapore, United States; Kronner: Denmark, Norway, Sweden; Japanese Yen; Swiss Francs; South African Rand
            $sCurrencyCode = $aOrderData['currency'];
            $sLocaleCode = get_locale();
        } elseif (version_compare($woocommerce->version, '2.0', '>=')) {
            $sOrderNumber = $oOrder->get_order_number();
            $sDescription = 'Order '.$sOrderNumber;

            // Order amount in Cents
            $fOrderAmount = round($oOrder->get_total(), 2);
            $sCurrencyCode = $oOrder->order_currency;

            $sLocaleCode = get_locale();
        }

        $sReturnUrl = add_query_arg(['wc-api' => 'iccf_gateway_return', 'real_id' => $sOrderId], home_url('/'));
        $sReturnFailureUrl = add_query_arg(['wc-api' => 'iccf_gateway_return_failure', 'real_id' => $sOrderId], home_url('/'));
        $sNotifyUrl = add_query_arg(['wc-api' => 'iccf_gateway_notify', 'real_id' => $sOrderId], home_url('/'));

        // Setup message for the order announcement
        $oCashflows->setOrderId(date('ymdHis').'/'.$sOrderId);
        $oCashflows->setOrderDescription($sOrderNumber, $sDescription);
        $oCashflows->setOrderAmount($fOrderAmount);
        $oCashflows->setCurrencyCode($sCurrencyCode);
        $oCashflows->setLocale($sLocaleCode);
        $oCashflows->setPaymentMethod($this->_getPaymentCode());

        $oCashflows->setReturnUrl($sReturnUrl);
        $oCashflows->setReturnFailureUrl($sReturnFailureUrl);
        $oCashflows->setReportUrl($sNotifyUrl);

        $iUserId = get_current_user_id();

        if (iccf_abstract_class::getAdditionalDataOption() && $this->createAdditionalData($oOrder)) {
            if (!empty($this->aAdditionalData['billingAddress'])) {
                $oCashflows->setBillingData($this->aAdditionalData['billingAddress']);
            }

            if (!empty($this->aAdditionalData['billingIdentity'])) {
                $oCashflows->setBillingIdentity($this->aAdditionalData['billingIdentity']);
            }

            if (!empty($this->aAdditionalData['shippingAddress'])) {
                $oCashflows->setShippingData($this->aAdditionalData['shippingAddress']);
            }

            if (!empty($this->aAdditionalData['orderLines'])) {
                $oCashflows->setOrderLines($this->aAdditionalData['orderLines']);
            }

            $bStoreCustomerData = iccf_abstract_class::getStoreCustomerData();

            if ($bStoreCustomerData) {
                if ($iUserId > 0) {
                    $oCashflows->setStoreCustomerInformation($bStoreCustomerData);

                    $sMetadataCustomerReference = iccf_abstract_class::getMetadataCustomerReference($iUserId);

                    if (!empty($sMetadataCustomerReference)) {
                        $oCashflows->setCustomerReference($sMetadataCustomerReference);
                    }
                }
            }
        }

        $bSendAnalyticsData = iccf_abstract_class::getGoogleAnalyticsOption();

        if ($bSendAnalyticsData) {
            if (!empty($_COOKIE['_ga'])) {
                $oCashflows->setAnalyticsCookie($_COOKIE['_ga']);
            }
        }

        if ($oCashflows->getTransaction()) {
            $aPaymentObject = json_decode($oCashflows->getJsonResponse(), true);

            if (iccf_abstract_class::getAdditionalDataOption()) {
                $bStoreCustomerData = iccf_abstract_class::getStoreCustomerData();

                if ($bStoreCustomerData) {
                    // Save or modify the customerReference
                    $sCustomerReference = $aPaymentObject['data']['order']['customerReference'];

                    $sMetadataCustomerReference = iccf_abstract_class::getMetadataCustomerReference($iUserId);

                    if (!empty($sMetadataCustomerReference)) {
                        // Received customerReference is different then the one saved.
                        if (strcasecmp($sCustomerReference, $sMetadataCustomerReference) !== 0) {
                            // What to do
                        }
                    } else {
                        // cashflows_customerReference doesnt exist, add to user
                        if (iccf_abstract_class::setMetadataCustomerReference($iUserId, $sCustomerReference)) {
                            $oOrder->add_order_note(__('Customer Reference added to the meta data', 'ic-cashflows-for-woo'));
                        } else {
                            $oOrder->add_order_note(__('Customer Reference could not be added to the current user, OR Current user is not logged in.', 'ic-cashflows-for-woo'));
                        }
                    }
                }
            }

            if ($sRedirectUrl = $oCashflows->getTransactionUrl()) {
                // Add note for chosen method:
                $oOrder->add_order_note(__('Cashflows payment started with', 'ic-cashflows-for-woo').': <b>'.$this->getPaymentName().'</b><br>'.__('Using Paymentjob', 'ic-cashflows-for-woo').':<br>'.$aPaymentObject['data']['reference']);

                // Add transaction ID
                $transactionId = $oCashflows->getTransactionId();
                $oOrder->update_meta_data('cf_transaction_id', $transactionId);

                $oOrder->save();

                if ($this->isRedirect()) {
                    return ['result' => 'success', 'redirect' => $sRedirectUrl];
                } else {
                    // Return thankyou redirect
                    return ['result' => 'success', 'redirect' => $this->get_return_url($oOrder)];
                }
            } else {
                $oOrder->add_order_note(__('We didn\'t receive the Transaction URL, this is the response', 'ic-cashflows-for-woo').': <br>'.$oCashflows->getJsonResponse());
            }
        } else {
            $aError = $oCashflows->getError();

            if ($aError['code'] == 'InvalidCustomerReference_1') {
                $oCashflows->setCustomerReference('');

                if ($oCashflows->getTransaction()) {
                    $aPaymentObject = json_decode($oCashflows->getJsonResponse(), true);

                    if (iccf_abstract_class::getAdditionalDataOption()) {
                        $bStoreCustomerData = iccf_abstract_class::getStoreCustomerData();

                        if ($bStoreCustomerData) {
                            // Save or modify the customerReference
                            $sCustomerReference = $aPaymentObject['data']['order']['customerReference'];

                            // cashflows_customerReference doesnt exist, add to user
                            if (iccf_abstract_class::setMetadataCustomerReference($iUserId, $sCustomerReference)) {
                                $oOrder->add_order_note(__('Customer Reference added to the meta data', 'ic-cashflows-for-woo'));
                            } else {
                                $oOrder->add_order_note(__('Customer Reference could not be added to the current user, OR Current user is not logged in.', 'ic-cashflows-for-woo'));
                            }
                        }
                    }

                    if ($sRedirectUrl = $oCashflows->getTransactionUrl()) {
                        // Add note for chosen method:
                        $oOrder->add_order_note(__('Cashflows payment started with', 'ic-cashflows-for-woo').': <b>'.$this->getPaymentName().'</b><br>'.__('Using Paymentjob', 'ic-cashflows-for-woo').':<br>'.$aPaymentObject['data']['reference']);
                        $oOrder->save();

                        if ($this->isRedirect()) {
                            return ['result' => 'success', 'redirect' => $sRedirectUrl];
                        } else {
                            // Return thankyou redirect
                            return ['result' => 'success', 'redirect' => $this->get_return_url($oOrder)];
                        }
                    } else {
                        $oOrder->add_order_note(__('We didn\'t receive the Transaction URL, this is the response', 'ic-cashflows-for-woo').': <br>'.$oCashflows->getJsonResponse());
                    }
                } else {
                    $aError = $oCashflows->getError();

                    if (!empty($aError)) {
                        wc_add_notice($aError['message'], 'error');
                    } else {
                        wc_add_notice(__('Unable to complete the transaction.', 'ic-cashflows-for-woo'), 'error');
                    }
                }
            } else {
                if (!empty($aError)) {
                    wc_add_notice($aError['message'], 'error');
                } else {
                    wc_add_notice(__('Unable to complete the transaction.', 'ic-cashflows-for-woo'), 'error');
                }
            }
        }
    }

    public static function doReturn()
    {
        global $woocommerce;

        if (empty($_GET['real_id']) || empty($_GET['paymentjobref'])) {
            wp_redirect(wc_get_cart_url());
        } else {
            $sOrderId = sanitize_text_field($_GET['real_id']);
            $sUtmOverride = array_key_exists('utm_nooverride', $_GET);

            $oOrder = wc_get_order($sOrderId);

            $oOrder->add_order_note(__('Customer Return: <b>Success</b>', 'ic-cashflows-for-woo'));
            wp_redirect($oOrder->get_checkout_order_received_url().($sUtmOverride ? '&utm_nooverride=1' : ''));
        }

        return false;
    }

    public static function doReturnFailure()
    {
        global $woocommerce;

        if (empty($_GET['real_id']) || empty($_GET['paymentjobref'])) {
            wp_redirect($woocommerce->cart->get_cart_url());
        } else {
            $sOrderId = sanitize_text_field($_GET['real_id']);
            $sUtmOverride = array_key_exists('utm_nooverride', $_GET);

            $oOrder = wc_get_order($sOrderId);

            if (array_key_exists('status', $_GET)) {
                $oOrder->add_order_note(__('Customer Return: <b>Cancelled</b>', 'ic-cashflows-for-woo'));
            } else {
                $oOrder->add_order_note(__('Customer Return: <b>Failure</b>', 'ic-cashflows-for-woo'));
            }

            wp_redirect(wc_get_cart_url().($sUtmOverride ? '&utm_nooverride=1' : ''));
        }

        return false;
    }

    public static function doNotify()
    {
        $sHtml = '';

        $sJsonData = @file_get_contents('php://input');
        $sOrderId = sanitize_text_field($_GET['real_id']);

        if (empty($sJsonData) || empty($sOrderId)) {
            // Set new response status
            http_response_code(406);
            header('HTTP/1.1 406 Not Acceptable');

            exit('Not Acceptable');
        } else {
            $aPostData = json_decode($sJsonData, true);

            if (isset($aPostData['paymentJobReference'])) {
                $iPaymentJobReference = sanitize_text_field($aPostData['paymentJobReference']);

                $sJsonResponse = json_encode([
                    'paymentJobReference' => sanitize_text_field($aPostData['paymentJobReference']),
                    'paymentReference' => sanitize_text_field($aPostData['paymentReference']),
                ]);
            } elseif (isset($aPostData['PaymentJobReference'])) {
                $iPaymentJobReference = sanitize_text_field($aPostData['PaymentJobReference']);

                $sJsonResponse = json_encode([
                    'PaymentJobReference' => sanitize_text_field($aPostData['PaymentJobReference']),
                    'PaymentReference' => sanitize_text_field($aPostData['PaymentReference']),
                ]);
            }

            $oCashflows = new CashflowsPayment(iccf_abstract_class::getApiKey(), iccf_abstract_class::getConfigurationId());
            $oCashflows->setTestmode(iccf_abstract_class::getDeveloperMode());

            $oOrder = wc_get_order($sOrderId);

            if (!$oOrder || ($oOrder->get_meta('cf_transaction_id') !== $iPaymentJobReference)) {
                http_response_code(406);
                header('HTTP/1.1 406 Not Acceptable');

                exit('Not Acceptable');
            }

            $sOrderStatus = $oOrder->get_status();
            $sTransactionStatus = $oCashflows->getStatus($iPaymentJobReference);

            if (empty($sTransactionStatus)) {
                $oOrder->add_order_note(__('Unable to fetch status from CashFlows Notify with Paymentjob', 'ic-cashflows-for-woo'));
                http_response_code(406);
                exit('Not Acceptable');
            }

            $sPaymentMethod = $oCashflows->getUsedPaymentMethod();

            if (iccf_abstract_class::getDeveloperMode()) {
                $sMessage = $oCashflows->getJsonResponse();
                $oOrder->add_order_note($sMessage);
            }

            if ((strcasecmp($sTransactionStatus, 'SUCCESS') === 0) && in_array($sOrderStatus, ['pending', 'failed', 'cancelled', 'on-hold'])) {
                $oOrder->add_order_note(__('Status received from CashFlows Notify (status changed)', 'ic-cashflows-for-woo').': '.$sTransactionStatus.' '.__('with Paymentjob', 'ic-cashflows-for-woo').':<br>'.$iPaymentJobReference.'<br>'.__('Payment-method', 'ic-cashflows-for-woo').': <b>'.$sPaymentMethod.'</b>');
                $oOrder->payment_complete($iPaymentJobReference);
            } elseif (strcasecmp($sTransactionStatus, 'SUCCESS') === 0) {
                // Payment was succesful, but order had a state that we cannot change
                $oOrder->add_order_note(__('Status received from CashFlows Notify', 'ic-cashflows-for-woo').': '.$sTransactionStatus.' '.__('with Paymentjob', 'ic-cashflows-for-woo').':<br>'.$iPaymentJobReference.'<br>'.__('Payment-method', 'ic-cashflows-for-woo').': <b>'.$sPaymentMethod.'</b>.'.__('But order had a state that we cannot change.', 'ic-cashflows-for-woo'));
            } else {
                $oOrder->add_order_note(__('Status received from CashFlows Notify', 'ic-cashflows-for-woo').': '.$sTransactionStatus.' '.__('with Paymentjob', 'ic-cashflows-for-woo').':<br>'.$iPaymentJobReference.'<br>'.__('Payment-method', 'ic-cashflows-for-woo').': <b>'.$sPaymentMethod.'</b>');

                // Check if WooCommerce cancels automatically for the stock management
                $iHoldStockMinutes = get_option('woocommerce_hold_stock_minutes');

                if (!empty($iHoldStockMinutes) && ($iHoldStockMinutes > 0)) {
                    // Happens automatically, we dont need to do anything
                    $sMessage = __('<br>If the payment is cancelled or expired, it will be cancelled automatically by WooCommerce .', 'ic-cashflows-for-woo');
                    $oOrder->add_order_note($sMessage);
                } else {
                    if (strcmp($sTransactionStatus, 'FAILURE') === 0) {
                        $sMessage = __('<br>Update order status: Failed.', 'ic-cashflows-for-woo');
                        $oOrder->add_order_note($sMessage);

                        $oOrder->update_status('failed');
                    } elseif (strcmp($sTransactionStatus, 'CANCELLED') === 0) {
                        $sMessage = __('<br>Update order status: Cancelled', 'ic-cashflows-for-woo');
                        $oOrder->add_order_note($sMessage);

                        $oOrder->update_status('cancelled');
                    } else {
                        // Possibly another status to be implemented?
                        $oOrder->add_order_note($sMessage);
                    }
                }
            }

            http_response_code(200);
            header('HTTP/1.1 200 OK');

            exit($sJsonResponse);
        }

        return false;
    }

    public function doRefund($sOrderId, $fAmount, $sReason)
    {
        $aResponse = [];

        if (!empty($sOrderId) && !empty($fAmount)) {
            $oOrder = wc_get_order($sOrderId);

            if ($oOrder) {
                $sTransactionId = $oOrder->get_meta('cf_transaction_id');

                if (!empty($sTransactionId)) {
                    $oCashflows = new CashflowsPayment(iccf_abstract_class::getApiKey(), iccf_abstract_class::getConfigurationId());
                    $oCashflows->setTestmode(iccf_abstract_class::getDeveloperMode());

                    $oCashflows->setPaymentJobReference($sTransactionId);
                    $aPaymentJob = $oCashflows->getPaymentJob();

                    if ($aPaymentJob && sizeof($aPaymentJob)) {
                        if (isset($aPaymentJob['data']['payments']) && isset($aPaymentJob['data']['payments'][count($aPaymentJob['data']['payments']) - 1]) && isset($aPaymentJob['data']['payments'][count($aPaymentJob['data']['payments']) - 1]['reference'])) {
                            $oCashflows->setPaymentReference($aPaymentJob['data']['payments'][count($aPaymentJob['data']['payments']) - 1]['reference']);
                            $oCashflows->setRefundAmount($fAmount);

                            if (empty($sReason)) {
                                $sReason = 'WOO Refund requested';
                            }

                            $oCashflows->setRefundDescriptor($sReason);
                            iccf_abstract_class::$oRefund->set_reason($sReason);

                            if ($oCashflows->doRefund()) {
                                $aResponse['data'] = $oCashflows->getJsonResponse();

                                $oOrder->add_order_note(__('Refund succesfully done for Paymentjob', 'ic-cashflows-for-woo').': '.$sTransactionId);
                            } else {
                                $aResponse['errors'] = $oCashflows->getJsonResponse();

                                $oOrder->add_order_note(__('Refund failed for Paymentjob', 'ic-cashflows-for-woo').': '.$sTransactionId);
                            }
                        }
                    }
                }
            } else {
                throw new Exception(__('Invalid order.', 'woocommerce'));
            }
        }

        if (sizeof($aResponse) && !isset($aResponse['errors'])) {
            wp_send_json_success($aResponse);
        } else {
            wp_send_json_error($aResponse, 400); // {"success":false}
        }
    }
}
