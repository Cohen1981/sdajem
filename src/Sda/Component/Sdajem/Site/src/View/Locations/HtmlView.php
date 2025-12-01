<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\View\Locations;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Sda\Component\Sdajem\Administrator\Library\Collection\LocationsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\HtmlListViewInterface;
use SimpleXMLElement;

/**
 * @since       1.0.0
 */
class HtmlView extends BaseHtmlView implements HtmlListViewInterface
{
	/**
	 * An array of items
	 *
	 * @var  array
	 * @since 1.0.0
	 */
	protected LocationsCollection $items;

	/**
	 * The model state
	 *
	 * @var  Registry
	 * @since 1.0.0
	 */
	protected Registry $state;

	/**
	 * Form object for search filters
	 *
	 * @var  Form
	 * @since 1.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 * @since 1.0.0
	 */
	public $activeFilters;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.0.0
	 */
	public function display($tpl = null): void
	{
		$model = $this->getModel();
		$this->items = $model->getItems();

		$this->pagination = $model->getPagination();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();
		$this->return_page = base64_encode(Uri::getInstance());

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		if (!count($this->items) && $model->getIsEmptyState())
		{
			$this->setLayout('emptystate');
		}

		// In article associations modal we need to remove language filter if forcing a language.
		// We also need to change the category filter to show show categories with All or the forced language.
		if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
		{
			// If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
			$languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
			$this->filterForm->setField($languageXml, 'filter', true);

			// Also, unset the active language filter so the search tools is not open by default with this filter.
			unset($this->activeFilters['language']);
		}

		parent::display($tpl);
	}

	/**
	 * Retrieves the collection of location list items.
	 *
	 * @return LocationsCollection The collection of location list items.
	 * @since 1.5.3
	 */
	public function getItems(): LocationsCollection
	{
		return $this->items;
	}

	/**
	 * Retrieves the state registry.
	 *
	 * @return Registry The state registry.
	 * @since 1.5.3
	 */
	public function getState(): Registry
	{
		return $this->state;
	}

	/**
	 * Retrieves the document instance.
	 *
	 * @return Document The document instance.
	 * @since 1.5.3
	 */
	public function getDocument():Document
	{
		return parent::getDocument();
	}
}
