<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Table;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Sda\Component\Sdajem\Administrator\Model\LocationModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Table
 * @since       1.0.0
 */
class FittingTable extends Table
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
		parent::__construct('#__sdajem_fittings', 'id', $db);
	}

	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function generateAlias()
	{
		/* @var LocationModel $this */
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}

	/**
	 * @since 1.0.0
	 * @return  boolean
	 * true on success, false on failure
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

		if (!$this->user_id)
		{
			$this->user_id = Factory::getApplication()->getIdentity()->id;
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
	public function store($updateNulls = true)
	{
		return parent::store($updateNulls);
	}
}
