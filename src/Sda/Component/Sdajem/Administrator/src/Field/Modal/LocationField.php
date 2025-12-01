<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Field\Modal;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ModalSelectField;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a modal location picker.
 *
 * @since  3.1
 */
class LocationField extends ModalSelectField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   1.6
	 */
	protected $type = 'Modal_Location';

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     FormField::setup()
	 * @since   5.1.0
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		// Check if the value consist with id:alias, extract the id only
		if ($value && str_contains($value, ':'))
		{
			[$id]  = explode(':', $value, 2);
			$value = (int) $id;
		}

		$result = parent::setup($element, $value, $group);

		if (!$result)
		{
			return $result;
		}

		if ($this->element['extension'])
		{
			$extension = (string) $this->element['extension'];
		}
		else
		{
			$extension = (string) Factory::getApplication()->getInput()->get('extension', 'com_sdajem');
		}

		Factory::getApplication()->getLanguage()->load('com_sdajem', JPATH_ADMINISTRATOR);

		$languages = LanguageHelper::getContentLanguages([0, 1], false);
		$language  = (string) $this->element['language'];

		// Prepare enabled actions
		$this->canDo['propagate']  = ((string) $this->element['propagate'] == 'true') && \count($languages) > 2;

		// Prepare Urls
		$linkItems = (new Uri)->setPath(Uri::base(true) . '/index.php');
		$linkItems->setQuery([
			'option'                => 'com_sdajem',
			'view'                  => 'locations',
			'layout'                => 'modal',
			'tmpl'                  => 'component',
			'extension'             => $extension,
			Session::getFormToken() => 1,
			]
		);
		$linkItem = clone $linkItems;
		$linkItem->setVar('view', 'location');

		if ($language)
		{
			$linkItems->setVar('forcedLanguage', $language);
			$linkItem->setVar('forcedLanguage', $language);

			$modalTitle = Text::_('COM_SDAJEM_CHANGE_LOCATION') . ' &#8212; ' . $this->getTitle();

			$this->dataAttributes['data-language'] = $language;
		}
		else
		{
			$modalTitle = Text::_('COM_SDAJEM_CHANGE_LOCATION');
		}

		$urlSelect = $linkItems;
		$urlEdit   = clone $linkItem;
		$urlEdit->setVar('task', 'location.edit');
		$urlNew    = clone $linkItem;
		$urlNew->setVar('task', 'location.add');

		$this->urls['select']  = (string) $urlSelect;
		$this->urls['new']     = (string) $urlNew;
		$this->urls['edit']    = (string) $urlEdit;

		// Prepare titles
		$this->modalTitles['select']  = $modalTitle;
		$this->modalTitles['new']     = Text::_('JNEW');
		$this->modalTitles['edit']    = Text::_('JEDIT');

		$this->hint = $this->hint ?: Text::_('COM_SDAJEM_CHANGE_LOCATION');

		return $result;
	}

	/**
	 * Method to retrieve the title of selected item.
	 *
	 * @return string
	 *
	 * @since   5.1.0
	 */
	protected function getValueTitle():string
	{
		$value = (int) $this->value ?: '';
		$title = '';

		if ($value)
		{
			try
			{
				$app = Factory::getApplication();
				$db    = $this->getDatabase();
				$query = $db->getQuery(true)
					->select($db->quoteName('title'))
					->from($db->quoteName('#__sdajem_locations'))
					->where($db->quoteName('id') . ' = :value')
					->bind(':value', $value, ParameterType::INTEGER);
				$db->setQuery($query);

				$title = $db->loadResult();
			}
			catch (\Throwable $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $title ?: $value;
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since 5.1.0
	 */
	protected function getLayoutData(): array
	{
		$data             = parent::getLayoutData();
		$data['language'] = (string) $this->element['language'];

		return $data;
	}

	/**
	 * Get the renderer
	 *
	 * @param   string  $layoutId  Id to load
	 *
	 * @return  FileLayout
	 *
	 * @since   5.1.0
	 */
	protected function getRenderer($layoutId = 'default'): FileLayout
	{
		$layout = parent::getRenderer($layoutId);
		$layout->setComponent('com_sdajem');
		$layout->setClient(1);

		return $layout;
	}
}
