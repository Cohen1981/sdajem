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
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;

defined('_JEXEC') or die();

/** @var \Sda\Component\Sdajem\Site\View\Attendings\HtmlView $this */

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns');
$wa->useScript('form.validate');
$wa->registerAndUseStyle('sdajem', 'com_sdajem/sdajem.css');

$canChange = true;
$canDo = ContentHelper::getActions('com_sdajem');

$listOrder = $this->escape($this->getState()->get('list.ordering'));
$listDirn  = $this->escape($this->getState()->get('list.direction'));

$params = $this->getParams();
$items = $this->getItems();
?>

<div class="sdajem_content_container">
	<form action="<?php echo Route::_('index.php?view=attendings'); ?>" method="post" name="adminForm" id="adminForm">
		<div>
			<?php if ($canDo->get('core.create')) : ?>
				<div class="mb-2">
					<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('attending.add')">
						<span class="fas fa-plus-circle" aria-hidden="true"></span>
						<?php echo Text::_('COM_SDAJEM_ATTENDING_ADD'); ?>
					</button>
				</div>
			<?php endif; ?>
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
						<table class="table table-striped" id="locationList">
							<caption class="visually-hidden">
								<?php echo Text::_('COM_SDAJEM_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
							</caption>
							<thead>
							<tr>
								<th scope="col" style="width:1%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_NAME', 'startDateTime', $listDirn, $listOrder); ?>
								</th>
                                <th scope="col" style="width:1%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_USERNAME', 'attendeeName', $listDirn, $listOrder); ?>
                                </th>
							</tr>
							</thead>
							<tbody>
							<?php
							$n = count($items);
							foreach ($items as $i => $item) :
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<th scope="row" class="has-context col-4">
										<div>
											<?php echo $this->escape($item->eventTitle); ?>
										</div>
                                        <div class="small">
                                            <?php echo HTMLHelper::date($item->startDateTime,'d.m.Y',true) . ' - '; ?>
                                            <?php echo HTMLHelper::date($item->endDateTime,'d.m.Y',true); ?>
                                        </div>
									</th>
                                    <td class="d-md-table-cell">
                                        <div>
		                                    <?php echo $this->escape($item->attendeeName); ?>
                                        </div>
                                        <div>
		                                    <?php echo $item->statusEnum->getAttendingStatusBadge(); ?>
                                        </div>
                                    </td>
									<td class="small d-none d-md-table-cell">
										<?php if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->users_user_id == Factory::getApplication()->getIdentity()->id)) : ?>
											<div class="icons">
												<?php echo HTMLHelper::_('sdajemIcon.editAttending', $item, $params); ?>
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
