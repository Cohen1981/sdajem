<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/** @var Sda\Component\Sdajem\Site\View\Fittings\HtmlView $this */

$items = $this->getItems();

$app = Factory::getApplication();
?>

<div class="container-popup">
    <form action="<?php echo Route::_('index.php?option=com_sdajem&task=fitting.addFittingToEvent&eventId=' . $this->eventId); ?>"
          method="post" name="adminForm" id="adminForm" class="form-inline">
        <?php if (empty($items)) : ?>
            <div class="alert alert-warning">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-sm" id="fittingList">
                <thead>
                <tr>
                    <td style='width:1%' class='text-center'>
                        <?php
                        echo HTMLHelper::_('grid.checkall'); ?>
                    </td>
                    <th scope="col" class="col-sm-4" style="width:10%" class="text-start d-md-table-cell">
                        <?php
                        echo Text::_('COM_SDAJEM_TABLE_TABLEHEAD_USER_NAME');; ?>
                    </th>
                    <th class='col-sm-4'>
                        <?php
                        echo Text::_('COM_SDAJEM_TABLE_TABLEHEAD_FITTING_TITLE');; ?>
                    </th>
                    <th class='col-sm-4'>
                        <?php
                        echo Text::_('COM_SDAJEM_TABLE_TABLEHEAD_FITTING_SIZE');; ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $i => $item) : ?>
                    <tr class="row<?php
                    echo $i % 2; ?>">
                        <td class='text-center'>
                            <?php
                            echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class='col-sm-4'>
                            <?php echo $this->escape($item->userName); ?>
                        </td>
                        <td class='col-sm-4'>
                            <?php echo $this->escape($item->title); ?>
                        </td>
                        <td class='col-sm-4'>
                            <?php echo $this->escape($item->length) . ' x ' . $this->escape($item->width); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <button type='button'
                class='btn btn-primary'
                onclick="Joomla.submitbutton('fitting.addFittingToEvent', 'adminForm', false)"
        >
            <?php echo Text::_('COM_SDAJEM_FITTING_ADD_FITTING_TO_EVENT'); ?>
        </button>
        <input type="hidden" name="task" value="">
        <input type='hidden' name='boxchecked' value='0'>
        <input type='hidden' name='eventId' value='<?php echo $this->eventId; ?>'>
        <input type="hidden" name="callContext" value="<?php echo $this->callContext; ?>">
        <input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get('forcedLanguage', '', 'CMD'); ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
