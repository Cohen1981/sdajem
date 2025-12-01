<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

/**
 * Represents an item in the location table.
 * This class extends the base PHP stdClass and implements the ItemInterface,
 * providing a model for individual items within a location-related table.
 *
 * @copyright (C) 2023 Your Name or Your Organization
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @since         1.5.3
 */
class LocationTableItem extends ItemClass
{
	/**
	 * @var integer|null
	 * @since 1.5.3
	 * The primary Key of the table
	 */
	public ?int $id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Stores the joomla access level
	 */
	public ?int $access;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * The joomla alias for an item
	 */
	public ?string $alias;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Creation datetime of this item
	 */
	public ?string $created;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * User id of creator
	 */
	public ?int $created_by;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Joomla publishing state
	 */
	public ?int $published;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stores the publish-up date and time in string format
	 */
	public ?string $publish_up;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the date and time when the content will stop being published.
	 */
	public ?string $publish_down;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the state or status of an entity.
	 */
	public ?int $state;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Specifies the ordering of items.
	 */
	public ?int $ordering;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the title of the entity or item.
	 */
	public ?string $title;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the description or additional details.
	 */
	public ?string $description;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the URL, which might be null if not set.
	 */
	public ?string $url;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the name of the street plus the number.
	 */
	public ?string $street;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the postal code, which may include numeric or alphanumeric values.
	 */
	public ?string $postalCode;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the name of the city
	 */
	public ?string $city;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the state address associated with the entity.
	 */
	public ?string $stateAddress;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Country name
	 */
	public ?string $country;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Latitude and Longitude coordinates
	 */
	public ?string $latlng;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Unique identifier for the contact. Foreign key to #__contact_details
	 */
	public ?int $contactId;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the image file path or URL
	 */
	public ?string $image;
}
