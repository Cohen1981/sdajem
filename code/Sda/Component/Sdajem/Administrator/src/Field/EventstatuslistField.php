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
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;

/**
 * @package     Sda\Component\Sdajem\Administrator\Field
 *
 * @since       1.0.0
 */
class EventstatuslistField extends ListField
{
	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $type = 'Eventstatuslist';
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
			$headerTitle = Text::_('SDAJEM_SELECT_EVENT_STATUS');
		}
		else
		{
			$headerTitle = $this->element['header'];
		}

		$options[] = HTMLHelper::_('select.option', '', $headerTitle);

		// Build the field options.
		$options[] = HTMLHelper::_('select.option', EventStatusEnum::PLANING->value, Text::_(EventStatusEnum::PLANING->getStatusLabel()));
		$options[] = HTMLHelper::_('select.option', EventStatusEnum::OPEN->value, Text::_(EventStatusEnum::OPEN->getStatusLabel()));

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		$this->value = EventStatusEnum::OPEN->value;

		return $options;
	}
}
