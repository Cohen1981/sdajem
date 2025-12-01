<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Fittings;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Site\Model\FittingsModel;

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
	 * @var FittingsCollection
	 * @since  1.0.0
	 */
	protected FittingsCollection $items;

	/**
	 * @var string
	 * @since 1.5.3
	 */
	public string $return_page;

	/**
	 * @param   null  $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function display($tpl = null): void
	{
		/** @var FittingsModel $model */
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
	 * @return FittingsCollection The collection of items.
	 * @since 1.5.3
	 */
	public function getItems():FittingsCollection
	{
		return $this->items;
	}
}
