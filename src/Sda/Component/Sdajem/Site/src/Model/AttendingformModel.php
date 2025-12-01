<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

defined('_JEXEC') or die();

use Exception;
use JForm;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use stdClass;

/**
 * @since 1.2.0
 */
class AttendingformModel extends \Sda\Component\Sdajem\Administrator\Model\AttendingModel
{
	/**
	 * Model typeAlias string. Used for version history.
	 *
	 * @since  __DEPLOY_VERSION__
	 * @var  string
	 */
	public $typeAlias = 'com_sdajem.attending';
	/**
	 * Name of the form
	 *
	 * @since  __DEPLOY_VERSION__
	 * @var string
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
		return ($this->getState('return_page')) ? base64_encode($this->getState('return_page')) :
			base64_encode(Route::_('?view=attendings&task=display'));
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @param   string  $name     The table name. Optional.
	 *
	 * @return  Table  A Table object
	 * @throws  Exception
	 */
	public function getTable($name = 'Attending', $prefix = 'Administrator', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}
}
