<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Interface;

use stdClass;

/**
 * @since 1.5.3
 */
interface ItemInterface
{
	/**
	 * @since 1.5.3
	 *
	 * @param   array  $data  the data to convert to self
	 *
	 * @return self
	 */
	public static function createFromArray(array $data): self;

	/**
	 * @since 1.5.3
	 *
	 * @param   stdClass  $data  the data to convert to self
	 *
	 * @return self
	 */
	public static function createFromObject(stdClass $data = new stdClass): self;

	/**
	 * @since 1.5.3
	 * @return array
	 */
	public function toArray(): array;
}
