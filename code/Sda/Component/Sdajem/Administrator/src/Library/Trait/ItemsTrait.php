<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Trait;

use ArrayIterator;

/**
 * @package Sda\Component\Sdajem\Administrator\Trait
 * @since   1.4.0
 * For programming convenience only
 */
trait ItemsTrait
{
	/**
	 * @var array The array containing the typed items
	 * @since 1.5.3
	 */
	private array $items = [];

	/**
	 * @since 1.5.3
	 * @return ArrayIterator
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * @since 1.5.3
	 * @return integer
	 */
	public function count(): int
	{
		return count($this->items);
	}

}
