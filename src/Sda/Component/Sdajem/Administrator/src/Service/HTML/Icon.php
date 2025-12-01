<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Service\HTML;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Administrator\Library\Item\Event;
use Sda\Component\Sdajem\Administrator\Library\Item\Fitting;
use Sda\Component\Sdajem\Administrator\Library\Item\Location;
use Sda\Component\Sdajem\Site\Helper\RouteHelper;
use Sda\Component\Sdajem\Site\Model\AttendingModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Represents various utilities to generate and display HTML icons for different items like events,
 * locations, attending, and registering related to a Joomla component.
 *
 * Provides methods for generating HTML for editing or interaction buttons (icons) for entities.
 * @since 1.5.3
 */
class Icon
{
	/**
	 * The application
	 *
	 * @var    CMSApplication $application
	 * @since  __DEPLOY_VERSION__
	 */
	private $application;

	/**
	 * Service constructor
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   CMSApplication  $application  The application
	 */
	public function __construct(CMSApplication $application)
	{
		$this->application = $application;
	}

	/**
	 * Display an edit icon for the event.
	 * This icon will not display in a popup window, nor if the event is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   Event     $event    The event information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string   The HTML for the event edit icon.
	 * @throws Exception
	 * @since   __DEPLOY_VERSION__
	 *
	 */
	public static function edit(Event $event, Registry $params, array $attribs = [], bool $legacy = false): string
	{
		$uri = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Ignore if the state is negative (trashed).
		if ($event->published < 0)
		{
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		if (!isset($event->slug))
		{
			$event->slug = "";
		}

		$eventUrl = RouteHelper::getEventRoute($event->slug);
		$url      = $eventUrl . '&task=event.edit&id=' . $event->id . '&return=' . base64_encode($uri);

		if ($event->published == 0)
		{
			$overlib = Text::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = Text::_('JPUBLISHED');
		}

		if (!isset($event->created))
		{
			$date = HTMLHelper::_('date', 'now');
		}
		else
		{
			$date = HTMLHelper::_('date', $event->created);
		}

		if (!isset($event->created_by))
		{
			$author = '';
		}
		else
		{
			$author = Factory::getApplication()->getIdentity($event->created_by)->name;
		}

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= Text::sprintf('COM_SDAJEM_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));
		$icon    = $event->published ? 'edit' : 'eye-slash';

		$currentTimestamp = Factory::getDate()->format('Y-m-d H:i:s');

		if ((($event->publish_up > $currentTimestamp) && $event->publish_up != null)
			|| (($event->publish_down < $currentTimestamp) && $event->publish_down != null))
		{
			$icon = 'eye-slash';
		}

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_('COM_SDAJEM_EDIT_EVENT'), $overlib, 0, 0) . '"></span> ';
		$text             .= Text::_('JGLOBAL_EDIT');
		$attribs['title'] = Text::_('COM_SDAJEM_EDIT_EVENT');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Display an edit icon for the location.
	 * This icon will not display in a popup window, nor if the event is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   Location       $location  The event information
	 * @param   Registry|null  $params    The item parameters
	 * @param   array          $attribs   Optional attributes for the link
	 * @param   boolean        $legacy    True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string   The HTML for the event edit icon.
	 * @throws Exception
	 * @since   __DEPLOY_VERSION__
	 *
	 */
	public static function editLocation(Location $location, Registry $params = null, array $attribs = [], bool $legacy = false)
	{
		$uri = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Ignore if the state is negative (trashed).
		if ($location->published < 0)
		{
			return '';
		}

		if (!isset($location->slug))
		{
			$location->slug = "";
		}

		$locationUrl = RouteHelper::getLocationRoute($location->slug);
		$url         = $locationUrl . '&task=location.edit&id=' . $location->id . '&return=' . base64_encode($uri);

		if ($location->published == 0)
		{
			$overlib = Text::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = Text::_('JPUBLISHED');
		}

		if (!isset($location->created))
		{
			$date = HTMLHelper::_('date', 'now');
		}
		else
		{
			$date = HTMLHelper::_('date', $location->created);
		}

		if (!isset($created_by_alias) && !isset($location->created_by))
		{
			$author = '';
		}
		else
		{
			$author = Factory::getApplication()->getIdentity($location->created_by)->name;
		}

		$overlib          .= '&lt;br /&gt;';
		$overlib          .= $date;
		$overlib          .= '&lt;br /&gt;';
		$overlib          .= Text::sprintf('COM_FOOS_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));
		$icon             = $location->published ? 'edit' : 'eye-slash';
		$currentTimestamp = Factory::getDate()->format('Y-m-d H:i:s');

		if ((($location->publish_up > $currentTimestamp) && $location->publish_up != null)
			|| (($location->publish_down < $currentTimestamp) && $location->publish_down != null))
		{
			$icon = 'eye-slash';
		}

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_('COM_SDAJEM_EDIT_LOCATION'), $overlib, 0, 0) . '"></span> ';
		$text             .= Text::_('JGLOBAL_EDIT');
		$attribs['title'] = Text::_('COM_SDAJEM_EDIT_LOCATION');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * @since 1.0.1
	 *
	 * @param          $params
	 * @param   array  $attribs
	 * @param   false  $legacy
	 * @param          $attending
	 *
	 * @return mixed|string
	 * @throws Exception
	 */
	public static function editAttending($attending, $params, $attribs = [], $legacy = false)
	{
		$uri = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		// Show checked_out icon if the event is checked out by a different user
		if (!isset($attending->slug))
		{
			$attending->slug = "";
		}

		$attendingUrl = 'index.php?option=com_sdajem&view=attending&id=' . $attending->id;
		$url          = $attendingUrl . '&task=attending.edit&id=' . $attending->id . '&return=' . base64_encode($uri);

		$icon = 'edit';

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_('COM_FOOS_EDIT_ATTENDING'), '', 0, 0) . '"></span> ';
		$text             .= Text::_('JGLOBAL_EDIT');
		$attribs['title'] = Text::_('COM_SDAJEM_EDIT_ATTENDING');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * @param   Event                        $event     The event information
	 * @param   FittingTableItemsCollection  $fittings  The fittings
	 * @param   Registry|null                $params    The item parameters
	 * @param   array                        $attribs   The attributes for the link
	 * @param   false                        $legacy    True to use legacy images
	 *
	 * @return string
	 * @throws Exception
	 * @since 1.0.0
	 */
	public static function register(
		Event                       $event,
		FittingTableItemsCollection $fittings,
		Registry                    $params = null,
		array                       $attribs = [],
		bool                        $legacy = false
	): string
	{
		$user = Factory::getApplication()->getIdentity();
		$uri  = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Ignore if the state is negative (trashed).
		if ($event->published < 0)
		{
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		if (!isset($event->slug))
		{
			$event->slug = "";
		}

		$url = Route::_('index.php?option=com_sdajem');

		$text = '<form action="' . $url . '" method="post" id="adminForm" name="adminForm">';
		$text .= '<input type="hidden" name="event_id" value="' . $event->id . '"/>'
			. '<input type="hidden" name="return" value="' . base64_encode($uri) . '"/>'
			. '<input type="hidden" name="task" value=""/>';

		if (isset($params['callContext']))
		{
			$text .= '<input type="hidden" name="callContext" value="' . $params['callContext'] . '"/>';
		}

		$text .= HTMLHelper::_('form.token');

		$interest = AttendingModel::getAttendingToEvent($user->id, $event->id);

		if ($interest->statusEnum != IntAttStatusEnum::NA)
		{
			$text .= '<input type="hidden" name="attendingId" value="' . $interest->id . '"/>';
		}
		else
		{
			$interest         = new Attending;
			$interest->statusEnum = IntAttStatusEnum::NA;
		}

		$eventStatus = ($event->eventStatusEnum === EventStatusEnum::PLANING) ? EventStatusEnum::PLANING : EventStatusEnum::OPEN;

		foreach (IntAttStatusEnum::cases() as $status)
		{
			if ($status != IntAttStatusEnum::NA)
			{
				if ($eventStatus === EventStatusEnum::OPEN)
				{
					if ($status !== $interest->statusEnum || $interest->eventStatusEnum === EventStatusEnum::PLANING)
					{
						$text .= '<button type="button" class="sda_button_spacer btn ' . $status->getButtonClass(
						) . '" onclick="Joomla.submitbutton(\'' . $status->getAction() . '\')">'
							. '<span class="icon-spacer ' . $status->getIcon() . '" aria-hidden="true"></span>';
						$text .= Text::_($status->getAttendingButtonLabel()) . '</button>';
					}
				}
				elseif ($status !== $interest->statusEnum)
				{
					$text .= '<button type="button" class="sda_button_spacer btn ' . $status->getButtonClass(
					) . '" onclick="Joomla.submitbutton(\'' . $status->getAction() . '\')">'
						. '<span class="icon-spacer ' . $status->getIcon() . '" aria-hidden="true"></span>';
					$text .= Text::_($status->getInterestButtonLabel()) . '</button>';
				}
			}
		}

		$params = ComponentHelper::getParams('com_sdajem');
		$uf     = $params->get('sda_events_use_fittings');

		if ($uf && isset($fittings) && $interest->statusEnum != IntAttStatusEnum::POSITIVE && $event->eventStatusEnum != EventStatusEnum::PLANING)
		{
			$text .= '<div class="sda_row"> <div class="sda_attendee_container">';

			foreach ($fittings as $i => $fitting)
			{
				$text .= '<div class="card" style="width: 120px;">';
				$text .= HTMLHelper::image($fitting->image, '');
				$text .= '<div class="card-body">';
				$text .= '<h5 class="card-title">' . $fitting->title . '</h5>';
				$text .= '<p class="card-text">' . $fitting->description . '</p>';
				$text .= '<input type="checkbox" name="fittings[]" value="' . $fitting->id . '"';

				if ($fitting->standard == 1)
				{
					$text .= ' checked="true"/>';
				}
				else
				{
					$text .= '/>';
				}

				$text .= '</div></div>';
			}

			$text .= '</div></div>';
		}

		$text   .= '</form>';
		$output = $text;

		return $output;
	}

	/**
	 * Generates an edit link for a fitting item.
	 *
	 * @param   Fitting         $fitting  The fitting item to be edited.
	 * @param   Registry|null   $params   Optional parameters for the link. Default is null.
	 * @param   array           $attribs  Additional HTML attributes for the link. Default is an empty array.
	 * @param   bool            $legacy   Whether to use legacy mode. Default is false.
	 *
	 * @return  string  The HTML markup for the edit link.
	 * @since   1.5.1
	 */
	public static function editFitting(Fitting $fitting, Registry $params = null, array $attribs = [], bool $legacy = false):string
	{
		$uri = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		$attendingUrl = 'index.php?option=com_sdajem&view=fitting&layout=edit&id=' . $fitting->id;
		$url          = $attendingUrl . '&task=fitting.edit&id=' . $fitting->id . '&return=' . base64_encode($uri);

		$icon = 'edit';

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_('COM_SDAJEM_EDIT_FITTING'), '', 0, 0) . '"></span> ';
		$text             .= Text::_('JGLOBAL_EDIT');
		$attribs['title'] = Text::_('COM_SDAJEM_EDIT_FITTING');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}

	public static function switchEventStatus($event, EventStatusEnum $action, $attribs = [])
	{
		$uri = Uri::getInstance();

		// Set the link class
		// $attribs['class'] = 'sda_button_spacer btn btn-light';
		$attribs['class'] = 'dropdown-item';

		$eventUrl = 'index.php?option=com_sdajem&view=events';
		$url      = $eventUrl . '&task=' . $action->getEventAction(
		) . '&eventId=' . $event->id . '&return=' . base64_encode($uri);

		$icon = $action->getIcon();

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(
				Text::_($action->getStatusLabel()),
				'',
				0,
				0
			) . '">&nbsp;</span><span class="icon-text">' . Text::_($action->getStatusLabel()) . '</span> ';
		$attribs['title'] = Text::_($action->getStatusLabel());
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}
}
