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
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @since         1.0.1
 */
class IntereststatuslistField extends ListField
{
	/**
	 * The form field type.
	 * @var string
	 * @since 1.0.1
	 */
	protected $type = 'Intereststatuslist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.1
	 */
	protected function getOptions()
	{
		$options = [];

		$key = 'id';
		$value = 'title';

		// Add header.
		if (!$this->element['header'])
		{
			$header_title = Text::_('SDAJEM_SELECT_INTEREST_STATUS');
		}
		else
		{
			$header_title = $this->element['header'];
		}

		$options[] = HTMLHelper::_('select.option', '', $header_title);

		// Build the field options.
		foreach (IntAttStatusEnum::cases() as $status)
		{
			$options[] = HTMLHelper::_('select.option', $status->value, Text::_($status->getInterestStatusLabel()));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

}
