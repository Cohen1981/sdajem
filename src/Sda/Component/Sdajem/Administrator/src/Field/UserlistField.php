<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Field;

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;
use SimpleXMLElement;

/**
 * UserlistField class that extends the Joomla ListField.
 * This class is used to generate a dropdown field that lists users based on specific criteria.
 *
 * @since 1.0.1
 */
class UserlistField extends ListField
{
	/**
	 * The form field type.
	 * @var string
	 * @since 1.0.1
	 */
	protected $type = 'Userlist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws \Exception
	 * @since   1.0.1
	 */
	protected function getOptions(): array
	{
		$options = [];

		$key = 'id';
		$value = 'username';

		$currentUser = Factory::getApplication()->getIdentity();

		// Get the database object.
		$db = $this->getDatabase();

		// Get the query object
		$query = $db->getQuery(true);

		$query->select(
			[
				$db->quoteName('u.id', 'id'),
				$db->quoteName('u.username', 'username')
			]
		);
		$query->from($db->quoteName('#__users', 'u'));

		// Join over the user_group_map.
		$query->select($db->quoteName('ugm.group_id', 'group_id'))
			->join(
				'INNER',
				$db->quoteName('#__user_usergroup_map', 'ugm') . ' ON ' . $db->quoteName('ugm.user_id') . ' = ' . $db->quoteName('u.id')
			);

		// Join over the user_group.
		$query->select($db->quoteName('ug.title', 'title'))
			->join(
				'INNER',
				$db->quoteName('#__usergroups', 'ug') . ' ON ' . $db->quoteName('ug.id') . ' = ' . $db->quoteName('ugm.group_id')
			);

		$params = ComponentHelper::getParams('com_sdajem');

		$query->where($db->quoteName('ug.id') . ' = ' . (int) $params->get('sda_user_group_name'));

		if ($this->element instanceof SimpleXMLElement)
		{
			$attr = $this->element->attributes();
			$params = ComponentHelper::getParams('com_sdajem');

			if (!$params->get('sda_show_all_users'))
			{
				if ($attr->filter !== null && !$currentUser->authorise('core.manage', 'com_sdajem'))
				{
					$query->where($db->quoteName('u.id') . ' = ' . $currentUser->id);
				}
			}
		}

		// Set the query and get the result list.
		$db->setQuery($query);

		try
		{
			$items = $db->loadObjectList();
		}
		catch (ExecutionFailureException $e)
		{
			Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'error');
		}

		// Add header.
		if (!$this->element['header'])
		{
			$header_title = Text::_('SDAJEM_SELECT_USER');
		}
		else
		{
			$header_title=$this->element['header'];
		}

		$options[] = HTMLHelper::_('select.option', '', $header_title);

		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{
					$options[] = HTMLHelper::_('select.option', $item->$key, $item->$value);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

}
