<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Table;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Sda\Component\Sdajem\Administrator\Model\LocationModel;

/**
 * @package     Sda\Component\Sdajem\Administrator\Table
 * @since       1.0.0
 */
class LocationTable extends Table
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
		parent::__construct('#__sdajem_locations', 'id', $db);
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
		/* @var LocationModel $this */
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}

	/**
	 * @since 1.0.0
	 * @return boolean
	 */
	public function check(): bool
	{
		try
		{
			$app = Factory::getApplication();
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
			$this->created_by = $app->getIdentity()->id;
		}

		if (!$this->created)
		{
			$this->created = Date::getInstance()->toSql();
		}

		// Set publish_up, publish_down to null if not set
		if (!$this->publish_up)
		{
			$this->publish_up = null;
		}

		if (!$this->publish_down)
		{
			$this->publish_down = null;
		}

		if (!$this->published)
		{
			$this->published = 1;
		}

		return true;
	}
}
