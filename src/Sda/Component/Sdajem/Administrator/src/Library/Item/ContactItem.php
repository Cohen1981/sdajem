<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model\Item
 * @since       1.5.3
 * Representation of the database table #__sdajem_events.
 * All field types are database-compatible.
 */
class ContactItem extends ItemClass
{
	/**
	 * @var integer|null
	 * @since 1.5.3
	 * The primary Key of the table
	 */
	public ?int $id;


	/**
	 * @var string|null
	 * Represents the title or name of an entity.
	 * @since 1.5.3
	 */
	public ?string $name;

	/**
	 * @var string|null
	 * Holds the description text
	 * @since 1.5.3
	 */
	public ?string $con_position;

	/**
	 * @var string|null
	 * Stores the image data or identifier.
	 * @since 1.5.3
	 */
	public ?string $image;

	public ?string $email_to;

	public ?string $telephone;

	public ?string $mobile;

	public ?string $fax;

	public ?string $webpage;

	public ?string $address;

	public ?string $suburb;

	public ?string $state;

	public ?string $country;

	public ?string $postcode;

	public ?int $published;

	public ?string $slug;

	public ?int $catid;

	public ?string $language;

	public ?string $publish_up;

	public ?string $publish_down;
}
