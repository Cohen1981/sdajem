<?php 

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Field;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Provides a custom field type for displaying a list of events.
 * The `EventlistField` class extends the `ListField` class to include a
 * method for generating event-specific options to populate the field.
 * @since 1.0.1
 */
class EventlistField extends ListField
{
	/**
	 * The form field type.
	 * @var string
	 * @since 1.0.1
	 */
	protected $type = 'Eventlist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws \Exception
	 * @since   1.0.1
	 */
	protected function getOptions():array
	{
		$options = [];

		$key = 'id';

		// Get the database object.
		$db = $this->getDatabase();

		// Get the query object
		$query = $db->getQuery(true);

		$query->select(
			[
				$db->quoteName('e.id', 'id'),
				$db->quoteName('e.title', 'title'),
				$db->quoteName('e.startDateTime', 'startDate'),
				$db->quoteName('e.endDateTime', 'endDate')
			]
		);
		$query->from($db->quoteName('#__sdajem_events', 'e'));

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
			$header_title = Text::_('SDAJEM_SELECT_EVENT');
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
					$startDate = Factory::getDate($item->startDate,'UTC');
					$endDate = Factory::getDate($item->endDate,'UTC');
					$options[] = HTMLHelper::_('select.option', $item->$key, $item->title . ' ' . $startDate->format('d.m.Y') . ' - ' .$endDate->format('d.m.Y'));
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
