<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       version
 */

namespace Sda\Component\Sdajem\Administrator\Library\Interface;

use Joomla\CMS\Document\Document;
use Joomla\Registry\Registry;

/**
 * @since 1.5.3
 */
interface HtmlListViewInterface
{
	/**
	 *
	 * @return CollectionInterface
	 *
	 * @since 1.5.3
	 */
	public function getItems():CollectionInterface;
	/**
	 * Retrieves the current state.
	 *
	 * @return Registry|null The current state if available, or null if not set.
	 * @since 1.5.3
	 */
	public function getState():Registry|null;

	/**
	 * Retrieves the document instance.
	 *
	 * @return Document The document object associated with the method.
	 * @since 1.5.3
	 */
	public function getDocument():Document;
}
