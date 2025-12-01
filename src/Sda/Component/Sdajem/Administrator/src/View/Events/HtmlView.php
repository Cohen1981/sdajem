<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\View\Events;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\EventsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\CollectionInterface;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Administrator\Model\EventsModel;

/**
 * HTML View class for the Events feature.
 *
 * @since 1.0.0
 */
class HtmlView extends BaseHtmlView implements HtmlListViewInterface
{
	use HtmlViewTrait;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since 1.0.0
	 */
	protected EventsCollection $items;

	/**
	 * The model state
	 *
	 * @var  Registry
	 * @since 1.0.0
	 */
	protected Registry $state;

	/**
	 * Form object for search filters
	 *
	 * @var  Form
	 * @since 1.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 * @since 1.0.0
	 */
	public $activeFilters;

	/**
	 * Method to display the view.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 * @throws Exception
	 */
	public function display($tpl = null): void
	{
		/** @var EventsModel $model */
		$model = $this->getModel();

		$this->items         = $model->getItems();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state         = $model->getState();

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		if (!count($this->items) && $model->getIsEmptyState())
		{
			$this->setLayout('emptystate');
		}

		// We don't need a toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = Sidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.0.0
	 * @return  void
	 * @throws Exception
	 */
	protected function addToolbar(): void
	{
		$this->sidebar = Sidebar::render();

		$canDo = ContentHelper::getActions('com_sdajem', 'event');
		$user  = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = $this->getDocument()->getToolbar();

		ToolbarHelper::title(Text::_('COM_SDAJEM_EVENTS'), 'address event');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_sdajem', 'core.create')) > 0)
		{
			$toolbar->addNew('event.add');
		}

		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);
			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('events.publish')->listCheck(true);
			$childBar->unpublish('events.unpublish')->listCheck(true);
			$childBar->archive('events.archive')->listCheck(true);

			if ($user->authorise('core.admin'))
			{
				$childBar->checkin('events.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('events.trash')->listCheck(true);
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('events.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		if ($user->authorise('core.admin', 'com_sdajem') || $user->authorise('core.options', 'com_sdajem'))
		{
			$toolbar->preferences('com_sdajem');
		}
	}

	/**
	 *
	 * @return EventsCollection
	 *
	 * @since 1.5.3
	 */
	public function getItems(): EventsCollection
	{
		return $this->items;
	}
}
