<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\CommentsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Model\AttendingsModel;
use Sda\Component\Sdajem\Administrator\Model\CommentsModel;
use Sda\Component\Sdajem\Administrator\Model\FittingsModel;
use Sda\Component\Sdajem\Site\Model\LocationModel;
use Sda\Component\Sdajem\Site\Model\UserModel;

/**
 * @package     Sda\Component\Sdajem\Site\Model\Item
 * @since       1.4.0
 * For programming convenience, the class gives type hinting for the class properties.
 * For frontend use there are additional fields with information from foreign tables
 */
class Event extends EventTableItem
{
	/**
	 * @var EventStatusEnum|null
	 * Enum for the event status. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?EventStatusEnum $eventStatusEnum;

	/**
	 * @var Registry|null
	 * Registry for the event parameters. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?Registry $paramsRegistry;

	/**
	 * @var Location|null
	 * location
	 * The location of the event.
	 * @since 2.1.0
	 */
	public ?Location $location;

	/**
	 * @var UserModel|null
	 * organizer
	 * The organizer of the event.
	 * @since 2.1.0
	 */
	public ?UserModel $organizer;

	/**
	 * @var CommentsCollection|null
	 * commens to the event
	 * @since version 2.1.0
	 */
	public ?CommentsCollection $comments;

	/**
	 * @var array|null
	 * Array of SVGs for the event.
	 * @since 1.5.3
	 */
	public ?array $svgs = [];

	/**
	 * @var AttendingsCollection|null
	 * interests/ attendings to the event
	 * @since version 2.1.0
	 */
	public ?AttendingsCollection $interests;

	/**
	 * @var FittingsCollection|null
	 * fittings for the event
	 * @since version 2.1.0
	 */
	public ?FittingsCollection $eventFittings;

	/**
	 * @since 1.4.0
	 *
	 * @param   array  $data  the event data as an array
	 *
	 * @return self
	 */
	public static function createFromArray(array $data = []): static
	{
		$item = parent::createFromArray($data);

		$params = ComponentHelper::getParams('com_sdajem');

		$item->eventStatusEnum = (isset($data['eventStatus'])) ? EventStatusEnum::from($data['eventStatus']) : EventStatusEnum::from($item->eventStatus);
		$item->paramsRegistry = (isset($data['params'])) ? new Registry($data['params']) : new Registry($item->params);

		if (isset($data['location']))
		{
			$item->location = new Location($data['location']);
		}
		else
		{
			$locMod = new LocationModel();

			if (isset($data['sdajem_location_id']))
			{
				$item->location = $locMod->getItem($data['sdajem_location_id']);
			}
			elseif (isset($item->sdajem_location_id))
			{
				$locMod->getItem($item->sdajem_location_id);
			}
		}

		if (isset($data['organizerId']))
		{
			$item->organizer = new UserModel($data['organizerId']);
		}
		elseif (isset($item->organizerId))
		{
			$item->organizer = new UserModel($item->organizerId);
		}

		if ($params->get('sda_events_use_comments'))
		{
			$commentsModel  = new CommentsModel();
			$item->comments = $commentsModel->getCommentsToEvent($item->id);
		}

		if ($params->get('sda_use_attending'))
		{
			$attendingsModel = new AttendingsModel();
			$item->interests = $attendingsModel->getAttendingsToEvent($item->id);

			if ($params->get('sda_events_use_fittings'))
			{
				$fittingsModel = new FittingsModel();
				$item->eventFittings = $fittingsModel->getFittingsForEvent($item->id);
			}
		}

		$item->svgs = (isset($data['svg'])) ? (array) json_decode($data['svg']) : [];

		return $item;
	}
}
