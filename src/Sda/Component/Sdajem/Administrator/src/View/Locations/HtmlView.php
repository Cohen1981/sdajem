<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\View\Locations;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\LocationsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Administrator\Model\LocationsModel;
use SimpleXMLElement;

/**
 * Class HtmlView
 * Provides the structure for displaying HTML views, managing items, states, filters,
 * and toolbars. It contains methods to render the view and manage the user interface
 * elements such as the sidebar and toolbars.
 * @since 1.0.0
 */
class  HtmlView extends BaseHtmlView implements HtmlListViewInterface
{
	use HtmlViewTrait;

	/**
	 * An array of items
	 *
	 * @var  LocationsCollection
	 * @since 1.0.0
	 */
	protected LocationsCollection $items;

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
	public Form $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 * @since 1.0.0
	 */
	public array $activeFilters;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.0.0
	 */
	public function display($tpl = null): void
	{
		/** @var LocationsModel $model */
		$model = $this->getModel();

		$this->items = $model->getItems();
		$this->filterForm = $model->getFilterForm();
		;
		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();

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

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = Sidebar::render();
		}
		elseif ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
		{
			// If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
			$languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
			$this->filterForm->setField($languageXml, 'filter', true);

			// Also, unset the active language filter so the search tools is not open by default with this filter.
			unset($this->activeFilters['language']);
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		$this->sidebar = Sidebar::render();

		$canDo = ContentHelper::getActions('com_sdajem', 'category', $this->state->get('filter.category_id'));
		$user  = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = $this->getDocument()->getToolbar();

		ToolbarHelper::title(Text::_('COM_SDAJEM_LOCATIONS'), 'address location');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_sdajem', 'core.create')) > 0)
		{
			$toolbar->addNew('location.add');
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
			$childBar->publish('locations.publish')->listCheck(true);
			$childBar->unpublish('locations.unpublish')->listCheck(true);
			$childBar->archive('locations.archive')->listCheck(true);

			if ($user->authorise('core.admin'))
			{
				$childBar->checkin('locations.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('locations.trash')->listCheck(true);
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('locations.delete')
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
	 * @return LocationsCollection
	 *
	 * @since 1.5.3
	 */
	public function getItems(): LocationsCollection
	{
		return $this->items;
	}
}
