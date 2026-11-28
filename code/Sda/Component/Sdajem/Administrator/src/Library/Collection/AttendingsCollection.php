<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Collection;

use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Interface\CollectionInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
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
	 * @param   array|null  $items  The items to add to the collection
	 *
	 *@since 1.5.3
	 *
	 */
	public function __construct(?array $items = null)
	{
		parent::__construct($items);

		foreach ($items as $item)
		{
			$this->items[] = Attending::createFromObject($item);
		}
	}

	/**
	 * @param   IntAttStatusEnum  $status       the status of the attending to count
	 * @param   bool              $isInterest   if the attending is interest (true if interest, false if attending)
	 *
	 * @return int  the count of attendings
	 *
	 * @since version 2.1.0
	 */
	public function countAttendingStatus(IntAttStatusEnum $status, bool $isInterest = false)
	{
		$count =  0;

		/** @var Attending $item */
		foreach ($this->items as $item)
		{
			if ($item->statusEnum == $status
				&& (bool)$item->is_interest == $isInterest
				&& isset($item->users_user_id))
			{
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @param   bool  $isInterest
	 *
	 * @return int
	 *
	 * @since version 2.1.0
	 */
	public function countFeedback(bool $isInterest = false)
	{
		$count = 0;

		/** @var Attending $item */
		foreach ($this->items as $item)
		{
			if ((bool)$item->is_interest == $isInterest
				&& isset($item->users_user_id)
				&& $item->status !== IntAttStatusEnum::NA)
			{
				$count++;
			}
		}

		return $count;
	}
}
