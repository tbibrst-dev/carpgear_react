<?php
/**
 * Class InvoiceCustomerData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

use JsonSerializable;

/**
 * Invoice customer data.
 */
class InvoiceCustomer implements JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var ContactDetails
	 */
	private $contact_details;

	/**
	 * @var string
	 */
	private $vat_number;

	/**
	 * @var string
	 */
	private $eori_number;

	/**
	 * @var string
	 */
	private $pid_number;

	/**
	 * InvoiceCustomerData constructor.
	 *
	 * @param Address        $address         .
	 * @param ContactDetails $contact_details .
	 * @param string         $vat_number      .
	 * @param string         $eori_number     .
	 * @param string         $pid_number      .
	 */
	public function __construct( Address $address, ContactDetails $contact_details, $vat_number, $eori_number = '', $pid_number = '' ) {
		$this->address         = $address;
		$this->contact_details = $contact_details;
		$this->vat_number      = $vat_number;
		$this->eori_number     = $eori_number;
		$this->pid_number      = $pid_number;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'address'             => $this->address,
			'contactDetails'      => $this->contact_details,
			'valueAddedTaxNumber' => $this->vat_number ?? '',
			'eoriNumber'          => $this->eori_number ?? '',
			'pidNumber'           => $this->pid_number ?? '',
		];
	}

}
