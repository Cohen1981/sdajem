<?php


/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\View\Location;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\LocationTableItem;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Administrator\Model\LocationModel;

/**
 * Class HtmlView
 * Handles the display and management of views for the given component.
 *
 * @package  Joomla.Component
 * @since    1.0.0
 */
class HtmlView extends BaseHtmlView implements HtmlViewInterface
{
	use HtmlViewTrait;

	/**
	 * The \JForm object
	 *
	 * @var  Form
	 * @since  1.0.0
	 */
	protected mixed $form;

	/**
	 * The active item
	 *
	 * @var  LocationTableItem
	 * @since   1.0.0
	 */
	protected LocationTableItem $item;

	/**
	 * Display the view.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		/** @var LocationModel $model */
		$model      = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.0.0
	 * @return  void
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user   = Factory::getApplication()->getIdentity();
		$userId = $user->id;

		$isNew = ($this->item->id == 0);

		ToolbarHelper::title(
			$isNew ? Text::_('COM_SDAJEM_LOCATION_NEW') : Text::_('COM_SDAJEM_LOCATION_EDIT'),
			'address location'
		);

		// Since we don't track these assets at the item level, use the category id.
		$canDo = ContentHelper::getActions('com_sdajem', 'location');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($isNew && $user->authorise('core.create', 'com_sdajem'))
			{
				ToolbarHelper::apply('location.apply');
				ToolbarHelper::saveGroup(
					[
						['save', 'location.save'],
						['save2new', 'location.save2new']
					],
					'btn-success'
				);
			}

			ToolbarHelper::cancel('location.cancel');
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable   = $canDo->get('core.edit') || ($canDo->get(
						'core.edit.own'
					) && $this->item->created_by == $userId);
			$toolbarButtons = [];

			// Can't save the record if it's not editable
			if ($itemEditable)
			{
				ToolbarHelper::apply('location.apply');
				$toolbarButtons[] = ['save', 'location.save'];

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					$toolbarButtons[] = ['save2new', 'location.save2new'];
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create'))
			{
				$toolbarButtons[] = ['save2copy', 'location.save2copy'];
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);

			ToolbarHelper::cancel('location.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 *
	 * @return LocationTableItem
	 *
	 * @since 1.5.3
	 */
	public function getItem(): LocationTableItem
	{
		return $this->item;
	}
}
