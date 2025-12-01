<?php
/**
 * @package     Sda\Component\Sdajem\Site\Helper
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Helper;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Site\Model\EventAttendeeModel;

/**
 * @since   1.5.3
 */
abstract class EventHtmlHelper
{
	/**
	 * Renders an attendee's information in the form of a styled card.
	 *
	 * @param   EventAttendeeModel  $attendeeModel  An object representing the attendee, containing user and event-related data.
	 * @param   string|null         $fieldName      Optional field name to display specific user data from the attendee model.
	 *
	 * @return void This method directly outputs the rendered HTML content.
	 * @since 1.5.3
	 */
	public static function renderAttendee(EventAttendeeModel $attendeeModel, string $fieldName = null)
	{
		echo '<div class="card" style="width: 120px;">';

		if (!is_null($fieldName))
		{
			echo $attendeeModel->userData[$fieldName]->value;
		}

		echo '<div class="card-body">';
		echo '<h5 class="card-title">' . $attendeeModel->user->username . '</h5>';

		if ($attendeeModel->event_status === EventStatusEnum::OPEN)
		{
			echo '<p class="card-text">' . $attendeeModel->status->getAttendingStatusBadge() . '</p>';
		}
		else
		{
			echo '<p class="card-text">' . $attendeeModel->status->getInterestStatusBadge() . '</p>';
		}

		echo '</div></div>';
	}

	/**
	 * Renders a fitting object as a styled card with an image, title, and description.
	 *
	 * @param   object  $fitting  The fitting object containing image, title, and description properties.
	 *
	 * @return void Outputs the HTML content directly to the page.
	 * @since 1.5.3
	 */
	public static function renderFitting($fitting)
	{
		echo '<div class="card" style="width: 120px;">';
		echo HTMLHelper::image($fitting->image, '');
		echo '<div class="card-body">';
		echo '<h5 class="card-title">' . $fitting->title . '</h5>';
		echo '<p class="card-text">' . $fitting->description . '</p>';
		echo '</div></div>';
	}
}
