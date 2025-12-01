<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Site\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Sda\Component\Sdajem\Administrator\Library\Item\Fitting;
use Sda\Component\Sdajem\Administrator\Model\FittingModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

/**
 * @since 1.1.5
 */
class FittingformModel extends FittingModel
{
	/**
	 * @var string
	 * @since 1.1.5
	 */
	protected $formName = 'form';

	/**
	 * Get the return URL.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @return  string  The return URL.
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @return  void
	 * @throws  Exception
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('fitting.id', $pk);
		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		$this->setState('layout', $app->input->getString('layout'));
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 * @throws  Exception
	 */
	public function getTable($name = 'Fitting', $prefix = 'Administrator', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}
}
