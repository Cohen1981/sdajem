<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Table;

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Table
 * @since       1.0.0
 */
class CommentTable extends Table
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
		parent::__construct('#__sdajem_comments', 'id', $db);
	}

	/**
	 * Check the table data for validity.
	 *
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
			$this->users_user_id = Factory::getApplication()->getIdentity()->id;
		}

		if (!$this->timestamp)
		{
			$this->timestamp = Date::getInstance()->toSql();
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
		return parent::store($updateNulls);
	}
}
