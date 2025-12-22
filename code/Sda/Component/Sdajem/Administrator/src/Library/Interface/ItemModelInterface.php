<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       version
 */

namespace Sda\Component\Sdajem\Administrator\Library\Interface;

/**
 * @since 1.5.3
 */
interface ItemModelInterface
{
	/**
	 * @param   int|null  $pk  The primary key
	 *
	 * @return ItemInterface
	 *
	 * @since version
	 */
	public function getItem(int $pk = null): ItemInterface;
}
