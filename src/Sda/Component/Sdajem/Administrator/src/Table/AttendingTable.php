<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Table;

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Table
 * Fields
 * @since       1.0.0
 * @property  int    id
 * @property  int    $access
 * @property  string alias
 * @property  int    state
 * @property  int    ordering
 * @property  int    event_id
 * @property  int    users_user_id
 * @property  int    status
 * @property  string fittings
 * @property  int    event_status
 */
class AttendingTable extends Table
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
		parent::__construct('#__sdajem_attendings', 'id', $db);
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
		$this->alias = 'attending';
		$this->alias = ApplicationHelper::stringURLSafe($this->alias);

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

		if (!$this->users_user_id)
		{
			$this->users_user_id = $app->getIdentity()->id;
		}

		return true;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param   bool  $updateNulls
	 *
	 * @return bool
	 */
	public function store($updateNulls = true): bool
	{
		if (is_array($this->fittings))
		{
			$registry       = new Registry($this->fittings);
			$this->fittings = (string) $registry;
		}

		return parent::store($updateNulls);
	}
}
