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

/**
 * Provides a custom field type for selecting contacts from the available contact list.
 * The field retrieves data from the database and constructs a list of options.
 * It extends the Joomla ListField class.
 * The field supports additional configuration via XML definitions, such as specifying
 * a header or merging parent options.
 * @since 1.0.1
 */
class ContactlistField extends ListField
{
	/**
	 * The form field type.
	 * @var string
	 * @since 1.0.1
	 */
	protected $type = 'Contactlist';

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
		$value = 'name';

		// Get the database object.
		$db = $this->getDatabase();

		// Get the query object
		$query = $db->getQuery(true);

		$query->select(
			[
				$db->quoteName('cd.id', 'id'),
				$db->quoteName('cd.name', 'name')
			]
		);
		$query->from($db->quoteName('#__contact_details', 'cd'));

		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join(
				'INNER',
				$db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('cd.catid')
			);

		$params = ComponentHelper::getParams('com_sdajem');

		$query->where($db->quoteName('c.id') . ' = ' . (int) $params->get('sda_host_category_name'));

		// Set the query and get the result list.
		$db->setQuery($query);

		try
		{
			$app = Factory::getApplication();
			$items = $db->loadObjectList();
		}
		catch (ExecutionFailureException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		}

		// Add header.
		if (!$this->element['header'])
		{
			$header_title = Text::_('SDAJEM_SELECT_CONTACT');
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
