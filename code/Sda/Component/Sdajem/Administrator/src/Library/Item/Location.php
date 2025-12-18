<?php
/**
 * @package     Sdajem.Administrator
 * @subpackage  com_sdajem
 *
 * @copyright   (C) 2025 Survivants d'Arce <https://www.survivants-d-acre.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Sda\Component\Sdajem\Administrator\Library\Item;

/**
 * Represents an item in a list of locations.
 * Extends the base functionality provided by LocationTableItem and implements the ItemInterface.
 * @since 1.5.3
 */
class Location extends LocationTableItem
{
	/**
	 * @var string|null
	 * Access level of the location.
	 * @since 1.5.3
	 */
	public ?string $accessLevel;
}
