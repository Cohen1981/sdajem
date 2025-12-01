<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Eventform;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Site\Model\EventformModel;

use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML Event View class for the Event component
 *
 * @since  __DEPLOY_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var    Form
	 * @since  __DEPLOY_VERSION__
	 */
	protected $form;

	/**
	 * @var \stdClass the Event item
	 * @since 1.0.0
	 */
	public \stdClass $item;

	/**
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $returnPage;

	/**
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pageclassSfx;

	/**
	 * @var    Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected $state;

	/**
	 * @var    Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed|void  A string if successful, otherwise an Error object.
	 *
	 * @throws Exception
	 * @since  __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$user = Factory::getApplication()->getIdentity();
		$app  = Factory::getApplication();
		/** @var EventformModel $model */
		$model            = $this->getModel();
		$this->state      = $model->getState();
		$this->item       = $model->getItem();
		$this->form       = $model->getForm();
		$this->returnPage = $model->getReturnPage();

		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_sdajem');
		}
		else
		{
			// Since we don't track these assets at the item level, use the category id.
			$authorised = $user->authorise('core.edit', 'com_sdajem') ||
				($user->authorise('core.edit.own', 'com_sdajem') &&
					$this->item->created_by == $user->id);
		}

		if ($authorised !== true)
		{
			$app->redirect('index.php?option=com_users&view=login');
		}

		// Create a shortcut to the parameters.
		$this->params = $this->state->params;

		// Escape strings for HTML output
		if ($this->params->get('pageclass_sfx'))
		{
			$this->pageclassSfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		}

		if (empty($this->item->id) && Multilanguage::isEnabled())
		{
			$lang = Factory::getApplication()->getLanguage()->getTag();
			$this->form->setFieldAttribute('language', 'default', $lang);
		}

		$this->returnPageEdit = base64_encode(Uri::getInstance());

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SDAJEM_FORM_EDIT_EVENT'));
		}

		$title = $this->params->def('page_title', Text::_('COM_SDAJEM_FORM_EDIT_EVENT'));

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->getDocument()->setTitle($title);
		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');
	}
}
