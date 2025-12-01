<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Trait;

use Joomla\CMS\Document\Document;
use Joomla\Registry\Registry;

/**
 * @package Sda\Component\Sdajem\Administrator\Trait
 * @author  Alexander Bahlo <abahlo@hotmail.de>
 * @since   1.4.0
 * For programming convenience only
 */
trait HtmlViewTrait
{
	/**
	 * @var Registry|null
	 * Represents the state or status, used to track or indicate conditions.
	 * @since 1.5.3
	 */
	protected Registry $state;

	/**
	 * Retrieves the state.
	 *
	 * @return Registry|null The state stored in the property.
	 * @since 1.5.3
	 */
	public function getState():Registry|null
	{
		return $this->state;
	}

	/**
	 * Retrieves the document instance.
	 *
	 * @since 1.5.3
	 * @return Document The document instance.
	 */
	public function getDocument(): Document
	{
		return parent::getDocument();
	}

	/**
	 * Retrieves the parameters stored in the Registry.
	 *
	 * @return Registry|null The parameters stored in the Registry.
	 * @since 1.5.3
	 */
	public function getParams():Registry|null
	{
		return $this->params;
	}
}
