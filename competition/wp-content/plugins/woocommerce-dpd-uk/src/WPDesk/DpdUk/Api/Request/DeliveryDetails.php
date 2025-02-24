<?php
/**
 * Class DeliveryDetailsData
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Delivery details data.
 */
class DeliveryDetails implements \JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var ContactDetails
	 */
	private $contact_details;

	/**
	 * @var NotificationDetails
	 */
	private $notification_details;

	/**
	 * @var PickupLocation|null
	 */
	private $pickup_location;

	/**
	 * CollectionDetailsData constructor.
	 *
	 * @param Address             $address .
	 * @param ContactDetails      $contact_details .
	 * @param NotificationDetails $notification_details .
	 * @param PickupLocation|null $pickup_location .
	 */
	public function __construct( Address $address, ContactDetails $contact_details, NotificationDetails $notification_details, $pickup_location = null ) {
		$this->address              = $address;
		$this->contact_details      = $contact_details;
		$this->notification_details = $notification_details;
		$this->pickup_location      = $pickup_location;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$serialized = [
			'address'             => $this->address,
			'contactDetails'      => $this->contact_details,
			'notificationDetails' => $this->notification_details,
		];
		if ( $this->pickup_location ) {
			$serialized['pickupLocation'] = $this->pickup_location;
		}
		return $serialized;
	}

}
