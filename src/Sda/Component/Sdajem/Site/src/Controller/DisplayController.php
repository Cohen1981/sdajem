<?php
/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Site\Controller
 *
 * @since       1.5.3
 */
class DisplayController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array                     $config   An optional associative array of configuration settings.
	 *                                              Recognized key values include 'name', 'default_task', 'model_path', and
	 *                                              'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface|null  $factory  The factory.
	 * @param   null                      $app      The JApplication for the dispatcher
	 * @param   null                      $input    Input
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  This object to support chaining.
	 *
	 * @throws \Exception
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = [])
	{
		$input = $this->input;

		$document   = $this->app->getDocument();
		$viewType   = $document->getType();

		switch ($input->get('view'))
		{
			case 'event':
				$view = $this->getView('event', $viewType);

				$view->setModel($this->getModel('event'), true);
				$view->setModel($this->getModel('location'));
				$view->setModel($this->getModel('comments'));
				$view->setModel($this->getModel('attendings', 'administrator'));
				$view->setModel($this->getModel('fittings', 'administrator'));
				break;
			case 'fittings':
				$view = $this->getView('fittings', $viewType);
				$view->setModel($this->getModel('fittings', 'administrator'), true);
				break;
			case 'attendings':
				$view = $this->getView('attendings', $viewType);
				$view->setModel($this->getModel('attendings', 'administrator'), true);
			default:
				break;
		}

		parent::display($cachable, $urlparams);

		return $this;
	}
}
