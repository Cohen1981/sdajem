<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Attendings;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;

/**
 * @since 1.5.3
 */
class HtmlView extends \Joomla\CMS\MVC\View\HtmlView implements HtmlListViewInterface
{
	use HtmlViewTrait;

	/**
	 * The page parameters
	 *
	 * @var    Registry|null
	 * @since  1.0.0
	 */
	protected Registry|null $params = null;

	/**
	 * The item model state
	 *
	 * @var    Registry
	 * @since  1.0.0
	 */
	protected Registry $state;

	/**
	 * The item object details
	 *
	 * @var    AttendingsCollection
	 * @since  1.0.0
	 */
	protected AttendingsCollection $items;
	/**
	 * @param   null  $tpl
	 *
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();
		$this->return_page = base64_encode(Uri::getInstance());

		parent::display($tpl);
	}

	/**
	 * Retrieves the collection of items.
	 *
	 * @return AttendingsCollection The collection of attending items.
	 * @since 1.0.0
	 */
	public function getItems(): AttendingsCollection
	{
		return $this->items;
	}
}
