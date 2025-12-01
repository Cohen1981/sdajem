<?php
/**
 * @package     Sda\Component\Sdajem\Site
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

defined('_JEXEC') or die();

/** @var \Sda\Component\Sdajem\Site\View\Fittings\HtmlView $this */

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns');
$wa->useScript('form.validate');
$wa->registerAndUseStyle('sdajem', 'com_sdajem/sdajem.css');

$canChange = true;
$canDo = ContentHelper::getActions('com_sdajem');
$currentUser = Factory::getApplication()->getIdentity();

$listOrder = $this->escape($this->getState()->get('list.ordering'));
$listDirn  = $this->escape($this->getState()->get('list.direction'));

$params = $this->getParams();

$items = $this->getItems();
?>

<div class="sdajem_content_container">
	<form action="<?php echo Route::_('index.php?view=fittings'); ?>" method="post" name="adminForm" id="adminForm">
		<div>
            <div class="mb-2">
                <?php if ($canDo->get('core.create')) : ?>
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('fitting.add')">
                    <span class="fas fa-plus-circle" aria-hidden="true"></span>
                    <?php echo Text::_('COM_SDAJEM_FITTING_ADD'); ?>
                </button>
                <?php endif; ?>
                <?php if ($canDo->get('core.delete')) : ?>
                <button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('fitting.delete')">
                    <span class="fas fa-trash" aria-hidden="true"></span>
                    <?php echo Text::_('COM_SDAJEM_FITTING_DELETE'); ?>
                </button>
                <?php endif; ?>
            </div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
					<?php if (empty($items)) : ?>
						<div class="alert alert-warning">
							<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
						</div>
					<?php else : ?>
						<table class="table table-striped" id="fittingsList">
							<caption class="visually-hidden">
								<?php echo Text::_('COM_SDAJEM_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
							</caption>
							<thead>
							<tr>
								<?php if (!$currentUser->guest) :?>
                                    <td style="width:1%" class="text-center d-sm-table-cell d-none">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
                                    </td>
								<?php endif; ?>
								<th scope="col" style="width:1%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_FITTING_TITLE', 'title', $listDirn, $listOrder); ?>
								</th>
                                <th scope="col" style="width:1%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_FITTING_USERNAME', 'userName', $listDirn, $listOrder); ?>
                                </th>
							</tr>
							</thead>
							<tbody>
							<?php
							$n = count($items);
							foreach ($items as $i => $item) :
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<?php if (!$currentUser->guest) :?>
                                        <td class="text-center d-sm-table-cell d-none">
											<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        </td>
									<?php endif; ?>
									<th scope="row" class="has-context col-4">
										<div>
											<?php echo $this->escape($item->title); ?>
										</div>
									</th>
                                    <td class="d-md-table-cell">
                                        <div>
		                                    <?php echo $this->escape($item->userName); ?>
                                        </div>
                                    </td>
									<td class="small d-none d-md-table-cell">
										<?php if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->user_id == Factory::getApplication()->getIdentity()->id)) : ?>
											<div class="icons">
												<?php echo HTMLHelper::_('sdajemIcon.editFitting', $item); ?>
											</div>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>

						<?php echo $this->pagination->getListFooter(); ?>

					<?php endif; ?>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
			</div>
	</form>
</div>
