<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class DisplayController
 * Controller class responsible for managing the display logic.
 * It extends the BaseController and supports overriding the display method.
 *
 * @since 1.0.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $default_view = 'events';
}
