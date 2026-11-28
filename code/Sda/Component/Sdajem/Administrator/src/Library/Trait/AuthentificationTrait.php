<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Trait;

use Joomla\CMS\Factory;

trait AuthentificationTrait
{
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   2.1.0
	 */
	protected function allowAdd($data = []): bool
	{
		try
		{
			$user = Factory::getApplication()->getIdentity();
		}
		catch (\Exception $e)
		{
			return false;
		}

		return $user->authorise('core.create', 'com_sdajem');

	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 * @since   2.1.0
	 *
	 */
	protected function allowEdit($data = [], $key = 'id'): bool
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return false;
		}

		// Need to do a lookup from the model.
		$record = $this->getModel()->getItem($recordId);

		try
		{
			$user = Factory::getApplication()->getIdentity();
		}
		catch (\Exception $e)
		{
			return false;
		}

		if ($user->authorise('core.edit', 'com_sdajem'))
		{
			return true;
		}

		// Fallback on edit.own.
		if ($user->authorise('core.edit.own', 'com_sdajem'))
		{
			return ($record->created_by == $user->id);
		}

		return false;
	}
}