<?php
/**
 * Class PickupLocation
 *
 * @package WPDesk\DpdUk\Api
 */

namespace WPDesk\DpdUk\Api\Request;

/**
 * Pickup Location.
 */
class PickupLocation implements \JsonSerializable {

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var bool
	 */
	private $allow_remote_pickup = false;

	/**
	 * @var string
	 */
	private $pickup_location_code;

	/**
	 * @param Address $address
	 * @param bool    $allow_remote_pickup
	 * @param string  $pickup_location_code
	 */
	public function __construct( Address $address, bool $allow_remote_pickup, string $pickup_location_code ) {
		$this->address              = $address;
		$this->allow_remote_pickup  = $allow_remote_pickup;
		$this->pickup_location_code = $pickup_location_code;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'address'            => $this->address,
			'allowRemotePickup'  => $this->allow_remote_pickup,
			'pickupLocationCode' => $this->pickup_location_code,
		];
	}

}
