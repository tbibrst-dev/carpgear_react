=== Cashflows for WooCommerce ===
Contributors: iDEAL Checkout, Cashflows
Tags: cashflows, woocommerce, creditcard, payment,
Requires at least: 5.8.0
Tested up to: 6.4.1
Stable tag: 2.1.9.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Cashflows Payments Gateway for WooCommerce

== Description ==

**Why choose the Cashflow payments Gateway?**

Cashflows is a simple and secure way to take payments online. When connecting Cashflows Gateway to WooCommerce, you will connect into all of the functionality that comes with our extension. Shoppers are directed to a customisable Hosted Payment Page where they securely complete their payment and we take care of the rest. Once the payment is successfully completed, the shopper is directed automatically back to your OpenCart site.

**Light touch integration to get your payments up and running faster**

Pre-integration between the Cashflows proprietary platform and WooCommerce means faster setup, lower costs. greater control and more secure data

**Smart data and reporting to unlock insight and optimise performance**

Track performance at-a-glance, with control over remittance, smart reconciliation and reporting built to deliver actionable insight for optimisation.

**Friendly service and expert support, at every interaction**

Our UK based team is always on hand and ready to act. We’ve got your back through implementation, we’ll keep your payments moving and make sure your data is handled securely.

**What our customers say**
“With Cashflows we have a true partnership. Our suggestions and concerns are always taken seriously and addressed. We have been delighted with the level of support we have experienced, and the team resolves any issues very quickly.”

SplitPay


== Installation ==

1. Upload the plugin via FTP to the folder `/wp-content/plugins/ic-cashflows-for-woo`, or use the Wordpress plug-in installation wizard.
2. Activate the plug-in via the 'plugins' screen in WordPress
3. Navigate to WooCommerce/Settings -> Payments and configure the plug-in/payment methods


== Frequently Asked Questions ==

= How do we establish our business | merchant account at Cashflows =

Our onboarding team is ready to bring you and your business(es) into the Cashflows. Look for us at (https://www.cashflows.com/apply-now).  We will, of 
course, conduct due diligence to ensure that both you and we are good partners to be doing business together.  But once approved, you will the
highest-level of service in the payments industry for your business.

= Can you provide my webshop with PCI DSS certified service =

We can provide your with payment solutions that leverage our PCI DSS certified services, so that you do not have to go through the certification 
process for your business.  We can through our javascript soluion or our customizable hosted payment page solutions allow your webshop to have fully
compliant PCI DSS certified card transactions processed for your business.  You can read our documentation (https://www.cashflows.com/support) or contact
our customer service to get your questions answered.

= What type of remittance | settlement options can I expect =

We have the most flexible remittance and settlement options in the payments services industry.  We can help your business find a settlement program
that allows you quick access to your completed transactions.  If timely Cashflow for you is a critical success factor, then you owe it to yourself
to talk with Cashflows.  We want to help you have better business opportunities because you have the cashflow to do it.

= When we are ready for paymemt options beyond cards, how do we proceed =

Tell us what your future payment processing needs are - we are ready to expand our payment selection to address customers demands.  We want to 
hear from you, so that we can provide payment solutions that allow you to convert orders into purchases.  With our own payments gateway standing
behind our WooCommerce plugin, we can bring solutions to you as the market evolves.

= How do we put our customers at ease with ecommerce =

The world has made ecommerce the fastest growing sector of the economy regardless of where you are located.  With recent events, this story will
only be accelerating.  The way that we can help you put your customers at ease is with some of the options available in our plugin.  

1 You can activate Secure 3DS which allows your customers to provide second factor authenication on the purchases.  
2 We can also save customer payment details in a secure manner, on your behalf, so that when customers return their payment details are already there - reinforcing the sense of connection to you and your business.
3 Use either our inline payment processing or relying on our customizable hosted payment page, we can safely complete the payment, helping
    keep your conversion rate high for your webshops.

== Screenshots ==

1. Cashflows Hosted Payment Page
2. Cashflows Go Dashboard
3. Cashflows Virtual Terminal


== Changelog ==

= 2.1.9.1 =
* Loaded gateways in the old method
* Removed compatibility with the WC blocks, because this was causing issues for merchants.

= 2.1.9 =
* Changed the way methods are loaded and made the installation smaller.
Do check that the Card method is active after updating!
* Added compatibility with the block checkout which is default with new installations.
* Checked compatibility with Wordpress 6.4.2 and Woocommerce 8.4.0, 8.5.0 and 8.5.1.

= 2.1.8 =
* Checked compatibility with Wordpress 6.4.1 and Woocommerce 8.3.0.

= 2.1.7 =
* Fixed an issue with the setting the Paymentjob in Woocommerce.
* Checked compatibility with Wordpress 6.3.1 & Woocommerce 8.1.1

= 2.1.6 =
* Fixed an issue where if the order could not be found, the get_transaction_id() function caused a fatal error.

= 2.1.5 =
* Checked compatibility with Wordpress 6.2.2 and WoooCommerce 7.7.2
* Fixed order notes being confusing when the status was succesful but still showed that the order will be cancelled automaticly if not paid.
* Added the transaction ID to the order for validation afterwards.

= 2.1.4 =
* Added a fix for customerReferences being passed which werent known to Cashflows for migrated customers

= 2.1.3 =
* Added a is_admin check to the filter woocommerce_payment_gateways_settings, it was being called for some customers on the frontend.
* Added on-hold as an order state which we can change with the Cashflows webhook/notify.
* Moved the loading of settings to the controller file, instead of the abstract class.

= 2.1.2 =
* Checked compatibility with Woocommerce 7.3.
* Added quick links in the plug-in overview to the support page and Settings of the plug-in.
* Fixed a notice that could occur when a customer returned from a failed payment, the status parameter could be missing.

= 2.1.1 =
* Fixed an error that could occur during the webhook call of Cashflows.

= 2.1.0 =
* Fixed a PHP notice when the user agent wasnt found.
* Fixed a PHP notice if the tax wasnt found for a product. 

= 2.0.9 =
* Checked compatibility with Woocommerce 6.6.0
* Settings werent showing, this has been fixed.

= 2.0.8 =
* Checked compatibility with Wordpress 6.0.
* Added checks for instantiating orders in Woocommerce.
* Updated store text

= 2.0.7 =
* Changed the webhook to support Woocommerce automaticly cancelling orders.
If the setting is not enabled we will cancel the order.
* Added new text to the store page.

= 2.0.6 =
* Fixed an issue where the installation would fail because of an redeclared class.

= 2.0.5 =
* Checked the plug-in for Wordpress 5.8.2 and WooCommerce 5.9.0
* Updated deprecated function
* Removed set_transaction_id, instead its added to the order on the payment_complete() function.
Refunds still work after this change, thank you for reporting David!

= 2.0.4 =
* Checked the plug-in for Wordpress 5.8.2 and WooCommerce 5.5.2
* Removed unused variables

= 2.0.3 =
* Initial release of the Cashflows WooCommerce plugin



== Arbitrary section ==


**Features**

Stay in control:

* Understand business performance, real-time
Create a personalised dashboard, compare performance over time, manage your account, all through a simple online portal.

* Keep your payments data safe and secure
With robust fraud prevention technology, our hosted payment pages include free PCI compliance. Plus 3DS 2.2 authentication as standard


Deliver a frictionless experience for your customers:

* A simple, intuitive checkout experience
Payment details can be securely stored and automatically filled in to make checkout fast and easy next time. 

* Maximise every sale
Reduce abandoned shopping carts by accepting the most popular payment types, including Visa, Mastercard, Amex and mobile wallets such as Apple Pay, Google Pay and PayPal. 

* Take payments from anywhere
Our gateway comes with a virtual terminal so you can take payments by phone or by email, to help you offer alternatives to customers where they want to pay”


**Security**

Safe, stable, secure checkouts

* PSD2 ready with built in security including 3DS2.2 authentication
* Built and managed in the cloud for ultimate stability
* Lightning-fast customer checkout 
* In-built SSL for safer communications
