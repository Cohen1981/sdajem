<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Location;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Event\Content\AfterDisplayEvent;
use Joomla\CMS\Event\Content\AfterTitleEvent;
use Joomla\CMS\Event\Content\BeforeDisplayEvent;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Location;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Site\Model\LocationModel;
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
	 * @var Location
	 * @since 1.5.3
	 */
	protected Location $item;

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
	public function display($tpl = null): void
	{
		$model = $this->getModel();
		$item  = $this->item = $model->getItem();

		$state  = $this->state = $model->getState();
		$params = $this->params = $state->get('params');

		/**
		 * $item->params are the event params, $temp are the menu item params
		 * Merge so that the menu item params take priority
		 * $itemparams->merge($temp);
		 */

		// Merge so that event params take priority
		$item->params = $params;

		$active = Factory::getApplication()->getMenu()->getActive();

		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		if ((!$active) || ((strpos($active->link, 'view=location') === false) ||
				(strpos($active->link, '&id=' . (string) $this->item->id) === false)))
		{
			if (($layout = $item->params->get('locations_layout')))
			{
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$contentEventArguments = [
			'context' => 'com_sdajem.location',
			'subject' => &$item,
			'params'  => &$item->params
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
	 *
	 * @return Location Returns the location item.
	 * @since 1.5.3
	 */
	public function getItem(): Location
	{
		return $this->item;
	}
}
