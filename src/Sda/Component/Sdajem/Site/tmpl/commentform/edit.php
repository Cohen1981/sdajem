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

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
$this->tab_name  = 'com-comment-form';
$this->ignore_fieldsets = ['details', 'item_associations', 'language'];
$this->useCoreUI = true;

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->useScript('com_sdajem.calendar');

?>
<div class="sdajem_content_container">
    <form action="<?php echo Route::_('index.php?option=com_sdajem&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
        <fieldset>
            <?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, ['active' => 'details']); ?>
            <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'details', empty($this->item->id) ? Text::_('COM_SDAJEM_NEW_LOCATION') : Text::_('COM_SDAJEM_EDIT_LOCATION')); ?>
            <?php echo $this->form->renderFieldset('comment'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
        <div class="mb-2 row g-4">
            <button type="button" class="btn btn-primary me-2 col-auto" onclick="Joomla.submitbutton('comment.save')">
                <span class="fas fa-check" aria-hidden="true"></span>
                <?php echo Text::_('JSAVE'); ?>
            </button>
        </div>
    </form>
</div>
