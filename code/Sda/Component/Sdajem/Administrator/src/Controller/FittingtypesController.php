<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @copyright (c) 2025 Alexander Bahlo
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @since         1.0.0
 */
class FittingtypesController extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 * @param   string  $name    The name of the model.
	 *
	 * @return  BaseDatabaseModel
	 */
	public function getModel($name = 'Fittingtype', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
