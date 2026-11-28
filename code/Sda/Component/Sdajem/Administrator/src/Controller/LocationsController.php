<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * LocationsController class handles administrative functionalities related to the locations of the Events component.
 *
 * @since     1.0.0
 */
class LocationsController extends AdminController
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
	public function getModel($name = 'Location', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
