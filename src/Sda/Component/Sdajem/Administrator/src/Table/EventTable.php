<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Table;

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Table
 * @since       1.0.0
 */
class EventTable extends Table
{
	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_sdajem';
		parent::__construct('#__sdajem_events', 'id', $db);
	}

	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function generateAlias(): string
	{
		if (empty($this->alias))
		{
			$this->alias = $this->title . HTMLHelper::date($this->startDateTime, 'd.m.Y');
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 * @throws Exception
	 */
	public function check(): bool
	{
		$app = Factory::getApplication();

		try
		{
			parent::check();
		}

		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->getDatabase()->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$app->enqueueMessage(Text::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'), 'warning');

			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = Factory::getApplication()->getIdentity()->id;
		}

		if (!$this->created)
		{
			$this->created = Date::getInstance()->toSql();
		}

		// Set publish_up, publish_down to null if not set
		if (!$this->publish_up)
		{
			$this->publish_up = Date::getInstance()->toSql();
		}

		if (!$this->publish_down)
		{
			$this->publish_down = null;
		}

		if (!$this->published)
		{
			$this->published = 1;
		}

		if (!$this->hostId)
		{
			$this->hostId = null;
		}

		if (!$this->organizerId)
		{
			$this->organizerId = null;
		}

		if (!$this->eventStatus)
		{
			$this->eventStatus = EventStatusEnum::OPEN->value;
			$this->access      = 1;
		}

		if ($this->eventStatus == EventStatusEnum::PLANING->value)
		{
			$this->access = 2;
		}

		if (!$this->registerUntil)
		{
			$this->registerUntil = null;
		}

		return true;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param   bool  $updateNulls  True to update fields even if they are null.
	 *
	 * @return boolean
	 */
	public function store($updateNulls = true): bool
	{
		// Transform the params field
		if (is_array($this->params))
		{
			$registry     = new Registry($this->params);
			$this->params = (string) $registry;
		}

		return parent::store($updateNulls);
	}
}
