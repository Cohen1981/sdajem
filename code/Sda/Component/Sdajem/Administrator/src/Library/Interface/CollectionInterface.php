<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Interface;

/**
 * @since 1.5.3
 */
interface CollectionInterface
{
	/**
	 * the constructor
	 *
	 * @since 1.5.3
	 *
	 * @param   array  $items  The items to add to the collection
	 */
	public function __construct(array $items = []);
}
