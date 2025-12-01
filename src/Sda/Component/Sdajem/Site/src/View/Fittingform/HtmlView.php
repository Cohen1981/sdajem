<?php
/**
 * @package     Sda\Component\Sdajem\Site\View
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Fittingform;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlViewInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\FittingTableItem;
use Sda\Component\Sdajem\Administrator\Library\Trait\HtmlViewTrait;
use Sda\Component\Sdajem\Site\Model\FittingformModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML Location View class
 *
 * @since  __DEPLOY_VERSION__
 */
class HtmlView extends BaseHtmlView implements HtmlViewInterface
{
	use HtmlViewTrait;

	/**
	 * @var    Form
	 * @since  __DEPLOY_VERSION__
	 */
	protected $form;

	/**
	 * @var    FittingTableItem | object
	 * @since  __DEPLOY_VERSION__
	 */
	protected FittingTableItem $item;

	/**
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public string $return_page;

	/**
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pageclass_sfx;

	/**
	 * @var    Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected Registry $state;

	/**
	 * @var    Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected Registry $params;

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

		$model = $this->getModel();
		$this->state = $model->getState();
		$this->item = $model->getItem();
		$this->form = $model->getForm();
		$this->return_page = $model->getReturnPage();

		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_sdajem');
		}
		else
		{
			$authorised = $user->authorise('core.edit', 'com_sdajem');

			if ($authorised !== true)
			{
				$authorised = $user->id == $this->item->user_id;
			}
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
			$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		}

		$this->return_page_edit = base64_encode(Uri::getInstance());

		$this->_prepareDocument();

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
	protected function _prepareDocument():void
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();

		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SDAJEM_FORM_EDIT_FITTING'));
		}

		$title = $this->params->def('page_title', Text::_('COM_SDAJEM_FORM_EDIT_FITTING'));

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

	/**
	 * @return FittingTableItem The item retrieved by this method.
	 * @since 1.5.3
	 */
	public function getItem():FittingTableItem
	{
		return $this->item;
	}
}
