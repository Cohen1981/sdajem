<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Table\Table;
use Sda\Component\Sdajem\Administrator\Library\Item\Location;
use Sda\Component\Sdajem\Administrator\Library\Item\LocationTableItem;

/**
 * @since  1.5.3
 */
class LocationformModel extends \Sda\Component\Sdajem\Administrator\Model\LocationModel
{
	/**
	 * Model typeAlias string. Used for version history.
	 *
	 * @var  string
	 * @since  __DEPLOY_VERSION__
	 */
	public $typeAlias = 'com_sdajem.location';

	/**
	 * Name of the form
	 *
	 * @var string
	 * @since  __DEPLOY_VERSION__
	 */
	protected   $formName = 'form';

	/**
	 * Method to get location data.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   integer  $pk  The id of the location.
	 *
	 * @return  mixed  Location item data object on success, false on failure.
	 * @throws  Exception
	 */
	public function getItem($pk = null):LocationTableItem
	{
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('locationform.id');

		return parent::getItem($pk);
	}

	/**
	 * Allows preprocessing of the JForm object.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 * @param   Form    $form   The form object
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function preprocessForm(Form $form, $data, $group = 'location')
	{
		if (!Multilanguage::isEnabled())
		{
			$form->setFieldAttribute('language', 'type', 'hidden');
			$form->setFieldAttribute('language', 'default', '*');
		}

		parent::preprocessForm($form, $data, $group);
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
	public function getTable($name = 'Location', $prefix = 'Administrator', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page', ''));
	}
}
