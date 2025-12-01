<?php

/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Sda\Component\Sdajem\Site\Model;

defined('_JEXEC') or die();

use Joomla\CMS\User\User;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;

/**
 * @since      1.0.0
 * @package     Sda\Component\Sdajem\Site\Model
 *
 */
class EventAttendeeModel extends UserModel
{
	/**
	 * @var integer|null
	 * @since 1.5.3
	 */
	public ?int $attendingId;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 */
	public ?int $event_id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 */
	protected ?int $users_user_id;

	/**
	 * @var IntAttStatusEnum|null
	 * @since 1.5.3
	 */
	public ?IntAttStatusEnum $status;

	/**
	 * @var EventStatusEnum
	 * @since 1.5.3
	 */
	public EventStatusEnum $event_status = EventStatusEnum::PLANING;

	/**
	 * Constructor method for initializing an object with data from an Attending instance.
	 *
	 * @param   Attending  $data  An object containing the necessary data for initialization.
	 *
	 * @since 1.5.3
	 */
	public function __construct(Attending $data)
	{
		if (isset($data->users_user_id))
		{
			parent::__construct($data->users_user_id);
			$this->users_user_id = $data->users_user_id;
		}

		$this->event_id = $data->event_id;
		$this->attendingId = $data->id;
		$this->status = $data->statusEnum;

		if (isset($data->eventStatusEnum))
		{
			$this->event_status = $data->eventStatusEnum;
		}
		else
		{
			$this->event_status = EventStatusEnum::tryFrom($data->event_status);
		}
	}
}
