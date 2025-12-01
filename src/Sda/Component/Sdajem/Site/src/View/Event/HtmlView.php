<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Event;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\Content\AfterDisplayEvent;
use Joomla\CMS\Event\Content\AfterTitleEvent;
use Joomla\CMS\Event\Content\BeforeDisplayEvent;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Contact\Administrator\Extension\ContactComponent;
use Joomla\Component\Contact\Site\Model\ContactModel;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\CommentsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Event;
use Sda\Component\Sdajem\Administrator\Library\Item\Location;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Site\Model\UserModel;
use stdClass;

/**
 * @method getMVCFactory()
 * @since __BUM_VERSION__
 */
class HtmlView extends BaseHtmlView implements HtmlViewInterface
{
	use HtmlViewTrait;

	/**
	 * The page parameters
	 *
	 * @var    Registry|null
	 * @since  1.0.0
	 */
	protected ?Registry $params = null;

	/**
	 * The item model state
	 *
	 * @var    Registry
	 * @since  1.0.0
	 */
	protected Registry $state;

	/**
	 * The item model
	 * @var Event|null
	 * @since 1.5.3
	 */
	protected ?Event $item = null;

	/**
	 * The event location if set
	 * @var Location|null
	 * @since 1.5.3
	 */
	protected ?Location $location = null;

	/**
	 * The User object of the organizer if set
	 * @var UserModel|null
	 * @since 1.5.3
	 */
	protected ?UserModel $organizer = null;

	/**
	 * The Contact object of the host if set
	 * @var ContactModel|null
	 * @since 1.5.3
	 */
	protected ?ContactModel $host = null;

	/**
	 * @var string
	 * @since 1.5.3
	 */
	public string $return_page;

	/**
	 * Attendings to the event
	 * @var AttendingsCollection|null
	 * @since 1.5.3
	 */
	protected ?AttendingsCollection $interests;

	/**
	 * Fittings of the users
	 * @var FittingTableItemsCollection|null
	 * @since 1.5.3
	 */
	protected ?FittingTableItemsCollection $userFittings = null;

	/**
	 * Fittings of the event
	 * @var FittingsCollection|null
	 * @since 1.5.3
	 */
	protected ?FittingsCollection $eventFittings = null;

	/**
	 * Comments to the event
	 * @var CommentsCollection|null
	 * @since 1.5.3
	 */
	protected ?CommentsCollection $comments = null;

	/**
	 * @var string
	 * @since 1.5.3
	 */
	public string $activeAccordion = 'event.location';

	/**
	 * Execute and display a template script.
	 *
	 * @since 1.0.0
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @throws Exception
	 */
	public function display($tpl = null):void
	{
		$model = $this->getModel();

		$app = Factory::getApplication();

		$item = $this->item = $model->getItem();

		$state      = $this->state = $model->getState();
		$params     = $this->params = $state->get('params');
		$itemParams = new Registry(json_decode($item->params));

		$activeAccordion       = $app->getUserState('com_sdajem.event.callContext', $this->activeAccordion);
		$this->activeAccordion = $activeAccordion;
		$app->setUserState('com_sdajem.event.callContext', '');

		$temp = clone $params;

		/**
		 * $item->params are the foo params, $temp are the menu item params
		 * Merge so that the menu item params take priority
		 * $itemparams->merge($temp);
		 */
		$temp->merge($itemParams);
		$item->paramsRegistry = $temp;
		$this->params = $temp;

		if (isset($item->organizerId))
		{
			$this->organizer = new UserModel($item->organizerId);
		}

		if (isset($item->hostId))
		{
			/** @var ContactComponent $contactComponent */
			$contactComponent = Factory::getApplication()->bootComponent('com_contact');

			$contactModel = $contactComponent->getMVCFactory()
				->createModel('Contact', 'Administrator', ['ignore_request' => true]);

			$temp       = $contactModel->getItem($item->hostId);
			$temp->slug = $temp->alias ? ($temp->id . ':' . $temp->alias) : $temp->id;
			$this->host = $temp;
		}

		if ($item->paramsRegistry->get('sda_events_use_comments'))
		{
			$commentsModel  = $this->getModel('comments');
			$this->comments = $commentsModel->getCommentsToEvent($item->id);
		}

		if ($item->paramsRegistry->get('sda_use_attending'))
		{
			$this->interests = $this->getModel('attendings')->getAttendingsToEvent($item->id);

			if ($item->paramsRegistry->get('sda_events_use_fittings'))
			{
				$this->userFittings  = $this->getModel('fittings')->getFittingsForUser();
				$this->eventFittings = $this->getModel('fittings')->getFittingsForEvent($item->id);
			}
		}

		if (isset($item->sdajem_location_id))
		{
			$this->location = $this->getModel('location')->getItem($item->sdajem_location_id);
		}

		$active = Factory::getApplication()->getMenu()->getActive();

		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		if (!$active || ((!str_contains($active->link, 'view=event')) || (!str_contains($active->link, '&id=' . $this->item->id))))
		{
			if (($layout = $item->paramsRegistry->get('events_layout')))
			{
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		if ($this->_layout === 'planning_modal')
		{
			$this->setLayout('planning_modal');
		}

		$contentEventArguments = [
			'context' => 'com_sdajem.event',
			'subject' => &$item,
			'params'  => &$item->paramsRegistry
		];

		$dispatcher = $this->getDispatcher();
		$dispatcher->dispatch('onContentPrepare', new ContentPrepareEvent('onContentPrepare', $contentEventArguments));

		// Store the events for later
		$item->event = new stdClass;

		$contentEvents = [
			'afterDisplayTitle'    => new AfterTitleEvent('onContentAfterTitle', $contentEventArguments),
			'beforeDisplayContent' => new BeforeDisplayEvent('onContentBeforeDisplay', $contentEventArguments),
			'afterDisplayContent'  => new AfterDisplayEvent('onContentAfterDisplay', $contentEventArguments),
		];

		foreach ($contentEvents as $resultKey => $event)
		{
			$results = $dispatcher->dispatch($event->getName(), $event)->getArgument('result', []);

			$item->event->{$resultKey} = $results ? trim(implode("\n", $results)) : '';
		}

		$this->return_page = base64_encode(Uri::getInstance());

		parent::display($tpl);
	}

	/**
	 * Retrieves an item based on defined criteria or conditions.
	 *
	 * @return Event The retrieved item or null if no item is found.
	 * @since 1.5.3
	 */
	public function getItem():Event
	{
		return $this->item;
	}

	/**
	 * Get the event location
	 *
	 * @return Location|null The location object or null if not set
	 * @since 1.5.3
	 */
	public function getLocation(): ?Location
	{
		return $this->location;
	}

	/**
	 * Get the event attendings
	 *
	 * @return AttendingsCollection|null The attendings collection or null if not set
	 * @since 1.5.3
	 */
	public function getInterests(): ?AttendingsCollection
	{
		return $this->interests;
	}

	/**
	 * Get the event host contact
	 *
	 * @return ContactModel|null The contact object or null if not set
	 * @since 1.5.3
	 */
	public function getHost(): ?ContactModel
	{
		return $this->host;
	}

	/**
	 * Get the event organizer
	 *
	 * @return UserModel|null The user object or null if not set
	 * @since 1.5.3
	 */
	public function getOrganizer(): ?UserModel
	{
		return $this->organizer;
	}

	/**
	 * Get the event fittings
	 *
	 * @return FittingsCollection|null The fittings collection or null if not set
	 * @since 1.5.3
	 */
	public function getEventFittings(): ?FittingsCollection
	{
		return $this->eventFittings;
	}

	/**
	 * Get the event comments
	 *
	 * @return CommentsCollection|null The comments collection or null if not set
	 * @since 1.5.3
	 */
	public function getComments(): ?CommentsCollection
	{
		return $this->comments;
	}

	/**
	 * Get the user fittings
	 *
	 * @return FittingTableItemsCollection|null The fittings collection or null if not set
	 * @since 1.5.3
	 */
	public function getUserFittings(): ?FittingTableItemsCollection
	{
		return $this->userFittings;
	}
}
