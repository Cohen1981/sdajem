<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Field;

defined('_JEXEC') or die();

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;

/**
 * Field class for creating a list of attending statuses.
 * This class inherits from the ListField and is specifically designed
 * to generate a drop-down list that includes various attending statuses.
 * These statuses are defined within the IntAttStatusEnum enumeration.
 * @since 1.0.1
 */
class AttendeestatuslistField extends ListField
{
	/**
	 * The form field type.
	 * @var string
	 * @since 1.0.1
	 */
	protected $type='Attendeestatuslist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.1
	 */
	protected function getOptions(): array
	{
		$options = [];

		$key = 'id';
		$value = 'title';

		// Add header.
		if (!$this->element['header'])
		{
			$header_title = Text::_('SDAJEM_SELECT_ATTENDING_STATUS');
		}
		else
		{
			$header_title=$this->element['header'];
		}

		$options[] = HTMLHelper::_('select.option', '', $header_title);

		// Build the field options.
		foreach (IntAttStatusEnum::cases() as $status)
		{
			$options[] = HTMLHelper::_('select.option', $status->value, Text::_($status->getAttendingStatusLabel()));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

}