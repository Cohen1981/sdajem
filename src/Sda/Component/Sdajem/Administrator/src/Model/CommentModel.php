<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\CommentTableItem;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.1.1
 */
class CommentModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_sdajem.comment';

	/**
	 * @since 1.0.0
	 *
	 * @param   bool   $loadData
	 * @param   array  $data
	 *
	 * @return Form|false
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		try
		{
			$form = $this->loadForm($this->typeAlias, 'comment', ['control' => 'jform', 'load_data' => $loadData]);
		}
		catch (Exception $e)
		{
			return false;
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @since   1.0.0
	 * @return  mixed  The data for the form.
	 * @throws Exception
	 */
	protected function loadFormData(): CommentTableItem
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_sdajem.edit.comment.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->typeAlias, $data);

		return new CommentTableItem($data);  // CommentTableItem::createFromObject($data);
	}

	/**
	 * @param   int|null  $pk The primary key.
	 *
	 * @return CommentTableItem
	 *
	 * @since 1.5.3
	 */
	public function getItem($pk = null): CommentTableItem
	{
		return CommentTableItem::createFromObject(parent::getItem($pk));
	}
}
