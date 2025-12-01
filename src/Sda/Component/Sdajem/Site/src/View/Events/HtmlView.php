<?php
/**
 * @package     Sda\Component\Sdajem\Site\View\Events
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Events;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Administrator\Library\Collection\EventsCollection;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * @package     Sda\Component\Sdajem\Site\View\Events
 *
 * @since       1.5.3
 */
class HtmlView extends BaseHtmlView implements HtmlListViewInterface
{
	use HtmlViewTrait;

	/**
	 * The page parameters
	 *
	 * @var    Registry|null
	 * @since  1.0.0
	 */
	public Registry|null $params = null;

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
	 * @var    EventsCollection
	 * @since  1.0.0
	 */
	protected EventsCollection $items;

	/**
	 * @var string
	 * @since 1.5.3
	 */
	public string $return_page;
	/**
	 * @param   null  $tpl
	 *
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function display($tpl = null)
	{
		$user = Factory::getApplication()->getIdentity();

		if ($user)
		{
			$template = $user->getParam('events_tpl', 'default');
			$this->setLayout($template);
		}

		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();
		$this->return_page = base64_encode(Uri::getInstance());

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		parent::display($tpl);
	}

	/**
	 *
	 * @return EventsCollection
	 *
	 * @since 1.5.3
	 */
	public function getItems():EventsCollection
	{
		return $this->items;
	}
}
