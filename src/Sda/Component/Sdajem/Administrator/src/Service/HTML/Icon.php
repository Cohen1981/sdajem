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
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemInterface;
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
	private CMSApplication $application;

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

		if (!isset($event->slug))
		{
			$event->slug = "";
		}

		$url = Route::_('?option=com_sdajem');

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

		/*
		 * sda_use_fittings have to be true and event status has to be everything but plning.
		 * If the has until now only shown interests, we show the fittings.
		 */
		if ($uf && isset($fittings) && $event->eventStatusEnum != EventStatusEnum::PLANING &&
			($interest->statusEnum != IntAttStatusEnum::POSITIVE ||
				($interest->statusEnum == IntAttStatusEnum::POSITIVE && $interest->eventStatusEnum == EventStatusEnum::PLANING)
			)
		)
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

		return $text;
	}


	/**
	 * Switches the status of an event and generates the corresponding action link
	 *
	 * @param   object           $event    The event object containing event information including the ID
	 * @param   EventStatusEnum  $action   The specific event status action to perform
	 * @param   array            $attribs  Additional HTML attributes for the generated link
	 *
	 * @return  string  The rendered HTML link for switching the event status
	 * @since   1.2.0
	 *
	 */
	public static function switchEventStatus($event, EventStatusEnum $action, $attribs = [])
	{
		$uri = Uri::getInstance();

		// Set the link class
		// $attribs['class'] = 'sda_button_spacer btn btn-light';
		$attribs['class'] = 'dropdown-item';

		$eventUrl = '?option=com_sdajem&view=events';
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

		return HTMLHelper::_('link', Route::_($url), $text, $attribs);
	}

	/**
	 * Generates an edit link for a given item with provided parameters.
	 *
	 * @param   ItemInterface  $item    The item for which the edit link is generated.
	 * @param   Registry|null  $params  An optional set of parameters for generating the edit link.
	 *                                  Needed parameters are:
	 *                                  - text: The text to be displayed for the edit link.
	 *                                  - view: The view to be used for the edit link which translates to the Controller in the task parameter.
	 *
	 * @return  string  The generated edit link HTML markup or an empty string if not applicable.
	 * @since   1.7.2
	 *
	 */
	public static function editLink(ItemInterface $item, Registry $params = null)
	{
		$uri     = Uri::getInstance();
		$overlib = '';

		// Ignore if in a popup window.
		if ($params && $params->get('popup') && isset($params['view']) && isset($params['text']))
		{
			return '';
		}

		// Ignore if the state is negative (trashed).
		if (!isset($item->published))
		{
			if ($item->published < 0)
			{
				return '';
			}
		}
		elseif ($item->published == 0)
		{
			$overlib = Text::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = Text::_('JPUBLISHED');
		}

		if (empty($item->slug))
		{
			$item->slug = $item->id;
		}

		$itemUrl = RouteHelper::getRoute($item->slug, $params['view']);
		$url     = 'index.php' .
			$itemUrl .
			'&task=' . $params['view'] . '.edit&id=' .
			$item->slug .
			'&return=' . base64_encode($uri);

		if (!isset($item->created))
		{
			$date = HTMLHelper::_('date', 'now');
		}
		else
		{
			$date = HTMLHelper::_('date', $item->created);
		}

		if (!isset($created_by_alias) && !isset($item->created_by))
		{
			$author = '';
		}
		else
		{
			$author = Factory::getApplication()->getIdentity($item->created_by)->name;
		}

		$overlib          .= '&lt;br /&gt;';
		$overlib          .= $date;
		$overlib          .= '&lt;br /&gt;';
		$overlib          .= Text::sprintf('COM_FOOS_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));
		$icon             = $item->published ? 'edit' : 'eye-slash';
		$currentTimestamp = Factory::getDate()->format('Y-m-d H:i:s');

		if ((($item->publish_up > $currentTimestamp) && $item->publish_up != null)
			|| (($item->publish_down < $currentTimestamp) && $item->publish_down != null))
		{
			$icon = 'eye-slash';
		}

		$text             = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_($params['text']), $overlib, 0, 0) . '"></span> ';
		$text             .= Text::_('JGLOBAL_EDIT');
		$attribs['title'] = Text::_($params['text']);

		return HTMLHelper::_('link', Route::_($url), $text, $attribs);
	}
}
