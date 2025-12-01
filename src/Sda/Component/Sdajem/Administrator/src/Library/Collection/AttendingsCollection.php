<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Collection;

use Sda\Component\Sdajem\Administrator\Library\Interface\CollectionInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Administrator\Library\Item\AttendingTableItem;
use Sda\Component\Sdajem\Administrator\Library\Trait\ItemsTrait;

/**
 * @since         1.5.3
 * @template-implements \IteratorAggregate<int, Attending>
 * Represents a collection of event table items, extending the functionality of the ArrayObject class.
 */
class AttendingsCollection extends \ArrayObject implements CollectionInterface
{
	use ItemsTrait;

	/**
	 * the constructor
	 *
	 * @since 1.5.3
	 *
	 * @param   array|null  $items  The items to add to the collection
	 */
	public function __construct(array $items = null)
	{
		parent::__construct($items);

		foreach ($items as $item)
		{
			$this->items[] = Attending::createFromObject($item);
		}
	}
}
