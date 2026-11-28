<?php

/**
 * @package     Sda\Component\Sdajem\Site\Enums
 * @subpackage  com_sdajem
 * @copyright (C) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Enums;

use Joomla\CMS\Language\Text;

/**
 * @since 1.0.0
 */
enum EventStatusEnum : int
{
	case OPEN = 0;
	case APPLIED = 1;
	case CONFIRMED = 2;
	case CANCELED = 3;
	case PLANING = 4;

	/**
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 * Joomla translate String for event status label
	 */
	public function getStatusLabel(): string
	{
		return match($this)
		{
			self::OPEN => 'COM_SDAJEM_EVENT_STATUS_OPEN',
			self::APPLIED => 'COM_SDAJEM_EVENT_STATUS_APPLIED',
			self::CONFIRMED => 'COM_SDAJEM_EVENT_STATUS_CONFIRMED',
			self::CANCELED => 'COM_SDAJEM_EVENT_STATUS_CANCELED',
			self::PLANING => 'COM_SDAJEM_EVENT_STATUS_PLANING'
		};
	}

	/**
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 * Actions for event status changes. Task to get status ATTENDING would be 'attending.attend'
	 */
	public function getEventAction(): string
	{
		return match($this)
		{
			self::OPEN => 'event.open',
			self::APPLIED => 'event.applied',
			self::CONFIRMED => 'event.confirmed',
			self::CANCELED => 'event.canceled',
			self::PLANING => 'event.planing'
		};
	}

	/**
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 * get the fontawesome class for a status symbol
	 */
	public function getIcon(): string
	{
		return match($this)
		{
			self::OPEN, self::PLANING => 'fas fa-question',
			self::APPLIED => 'fas fa-clipboard',
			self::CONFIRMED => 'fas fa-thumbs-up',
			self::CANCELED => 'fas fa-thumbs-down'
		};
	}

	/**
	 * Generates and returns a status badge as an HTML string based on the current object's state.
	 *
	 * @return string The HTML string representing the status badge with a corresponding color and label.
	 * @since 1.0.0
	 */
	public function getStatusBadge(): string
	{
		return match($this)
		{
			self::OPEN => '<span class="badge color-neutral eventbatch">' . Text::_('COM_SDAJEM_EVENT_STATUS_OPEN') . '</span>',
			self::APPLIED => '<span class="badge color-warning eventbatch">' . Text::_('COM_SDAJEM_EVENT_STATUS_APPLIED') . '</span>',
			self::CONFIRMED => '<span class="badge color-ok eventbatch">' . Text::_('COM_SDAJEM_EVENT_STATUS_CONFIRMED') . '</span>',
			self::CANCELED => '<span class="badge color-nok eventbatch">' . Text::_('COM_SDAJEM_EVENT_STATUS_CANCELED') . '</span>',
			self::PLANING => '<span class="badge color-neutral eventbatch">' . Text::_('COM_SDAJEM_EVENT_STATUS_PLANING') . '</span>'
		};
	}

	/**
	 * Determines the CSS color class corresponding to the current status.
	 *
	 * @return string The CSS class name representing the status color.
	 * @since 1.0.0
	 */
	public function getStatusColorClass(): string
	{
		return match($this)
		{
			self::OPEN, self::PLANING => 'color-neutral',
			self::APPLIED => 'color-warning',
			self::CONFIRMED => 'color-ok',
			self::CANCELED => 'color-nok'
		};
	}

	/**
	 * Retrieves the hexadecimal color code associated with the current status.
	 *
	 * @return string The color code representing the status.
	 * @since 1.0.0
	 */
	public function getColor(): string
	{
		return match($this)
		{
			self::OPEN, self::PLANING => '#c7cfd2',
			self::APPLIED => '#F9eb53',
			self::CONFIRMED => '#4db942',
			self::CANCELED => '#Bd181b'
		};
	}
}
