<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sdajem
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @ref Sda\Component\Sdajem\Site\View\Eventform\HtmlView $this */

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
$this->tab_name  = 'com-events-form';
$this->ignore_fieldsets = ['details', 'item_associations', 'language'];
$this->useCoreUI = true;

$wa = $this->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->useScript('com_sdajem.calendar');

?>
<div class="sdajem_content_container">
    <form action="<?php echo Route::_(
        'index.php?option=com_sdajem&id=' . (int) $this->item->id); ?>"
          method="post"
          name="adminForm"
          id="adminForm"
          class="form-validate form-vertical"
    >
        <fieldset>
            <?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, ['active' => 'details']); ?>
            <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'details', empty($this->item->id) ? Text::_('COM_SDAJEM_NEW_EVENT') : Text::_('COM_SDAJEM_EDIT_EVENT')); ?>
            <?php echo $this->form->renderField('title'); ?>
            <?php if (is_null($this->item->id)) : ?>
                <?php echo $this->form->renderField('alias'); ?>
            <?php endif; ?>
            <?php echo $this->form->renderField('eventStatus'); ?>

            <?php echo $this->form->renderField('description'); ?>
            <?php echo $this->form->renderField('image'); ?>
            <?php echo $this->form->renderField('url'); ?>

            <?php if ($this->params['sda_use_location']): ?>
                <?php echo $this->form->renderField('sdajem_location_id'); ?>
            <?php endif; ?>

            <?php echo $this->form->renderField('allDayEvent'); ?>
            <?php echo $this->form->renderField('startDateTime'); ?>
            <?php echo $this->form->renderField('endDateTime'); ?>

            <?php if ($this->params['sda_use_organizer']): ?>
                <?php echo $this->form->renderField('organizerId'); ?>
                <?php echo $this->form->renderField('registerUntil'); ?>
            <?php endif; ?>

            <?php if ($this->params['sda_use_host']): ?>
                <?php echo $this->form->renderField('hostId'); ?>
            <?php endif; ?>

            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php if ($this->params['sda_use_attending']): ?>
                <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'params', Text::_('COM_SDAJEM_EVENT_PARAMS')); ?>
                <?php echo $this->form->renderFieldset('display'); ?>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php endif; ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="return" value="<?php echo $this->returnPage; ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
        <div class="mb-2 row g-4">
            <button type="button" class="btn me-2 btn-primary col-auto" onclick="Joomla.submitbutton('event.save')">
                <span class="fas fa-check" aria-hidden="true"></span>
                <?php echo Text::_('JSAVE'); ?>
            </button>
            <button type="button" class="btn me-2 btn-primary col-auto" onclick="Joomla.submitbutton('event.save2new')">
                <span class="fas fa-check" aria-hidden="true"></span>
                <?php echo Text::_('SAVE_AND_NEW'); ?>
            </button>
            <button type="button" class="btn me-2 btn-danger col-auto" onclick="Joomla.submitbutton('event.cancel')">
                <span class="fas fa-times-cancel" aria-hidden="true"></span>
                <?php echo Text::_('JCANCEL'); ?>
            </button>
            <input type="hidden" name="return" value="<?php echo $this->returnPage; ?>"/>
        </div>
    </form>
    <form action="<?php echo Route::_('index.php?option=com_sdajem&id=' . (int) $this->item->id); ?>" method="post" name="locForm" id="locForm">
        <div class="mb-2 row g-4">
            <input type="hidden" name="return" value="<?php echo $this->returnPageEdit; ?>">
            <input type="hidden" name="task" value=""/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
    </form>
</div>
