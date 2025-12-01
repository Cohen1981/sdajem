<?php
/**
 * @package     Sda\Component\Sdajem\Administrator\Library\Interface
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
namespace Sda\Component\Sdajem\Administrator\Library\Interface;

use Joomla\CMS\Document\Document;
use Joomla\Registry\Registry;

/**
 * @since 1.5.3
 */
interface HtmlViewInterface
{
	/**
	 *
	 * @return ItemInterface
	 *
	 * @since 1.5.3
	 */
	public function getItem():ItemInterface;
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
