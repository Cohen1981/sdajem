<?php
/**
 * @package     Sda\Component\Sdajem\Site
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

/** @var \Sda\Component\Sdajem\Site\View\Fittingform\HtmlView $this */

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
$this->tab_name  = 'com-fitting-form';
$this->ignore_fieldsets = ['details', 'item_associations', 'language'];
$this->useCoreUI = true;

$canDo = ContentHelper::getActions('com_sdajem');

$item = $this->getItem();
?>
<div class="sdajem_content_container">
	<form action="<?php echo Route::_('index.php?option=com_sdajem&id=' . (int) $item->id); ?>" method="post" name="fittingForm" id="fittingForm" class="form-validate form-vertical">
		<fieldset>
			<?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, ['active' => 'details']); ?>
			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'details', empty($item->id) ? Text::_('COM_SDAJEM_NEW_FITTING') : Text::_('COM_SDAJEM_EDIT_FITTING')); ?>

			<?php echo $this->getForm()->renderFieldset('details'); ?>

            <?php if($canDo->get('core.manage')): ?>
	            <?php echo $this->getForm()->renderFieldset('admin'); ?>
            <?php endif; ?>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</fieldset>
		<div class="mb-2 row g-4">
			<button type="button" class="btn btn-primary me-2 col-auto" onclick="Joomla.submitbutton('fitting.save')">
				<span class="fas fa-check" aria-hidden="true"></span>
				<?php echo Text::_('JSAVE'); ?>
			</button>
			<button type="button" class="btn me-2 btn-primary col-auto" onclick="Joomla.submitbutton('fitting.save2new')">
				<span class="fas fa-check" aria-hidden="true"></span>
				<?php echo Text::_('SAVE_AND_NEW'); ?>
			</button>
			<button type="button" class="btn btn-danger me-2 col-auto" onclick="Joomla.submitbutton('fitting.cancel')">
				<span class="fas fa-times-cancel" aria-hidden="true"></span>
				<?php echo Text::_('JCANCEL'); ?>
			</button>

			<div class="input-group col">
				<input type="hidden" name="returnEdit" value="<?php echo $this->return_page_edit; ?>">
			</div>
		</div>
	</form>
</div>
