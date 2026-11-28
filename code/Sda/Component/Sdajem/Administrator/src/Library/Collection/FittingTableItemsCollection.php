<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Collection;

use Sda\Component\Sdajem\Administrator\Library\Interface\CollectionInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\FittingTableItem;
use Sda\Component\Sdajem\Administrator\Library\Trait\ItemsTrait;

/**
 * @since         1.5.3
 * @template-implements \IteratorAggregate<int, FittingTableItem>
 * Represents a collection of event table items, extending the functionality of the ArrayObject class.
 */
class FittingTableItemsCollection extends \ArrayObject implements CollectionInterface
{
	use ItemsTrait;

	/**
	 * the constructor
	 *
	 * @since 1.5.3
	 *
	 * @param   array|null  $items  The items to add to the collection
	 */
	public function __construct(array|null $items = [])
	{
		parent::__construct($items);

		foreach ($items as $item)
		{
			$this->items[] = FittingTableItem::createFromObject($item);
		}
	}
}
