<?php 


/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Sda\Component\Sdajem\Administrator\Model\Items\LocationsItemModel;

defined('_JEXEC') or die();

$wa = $this->document->getWebAssetManager();
$wa->useScript('bootstrap.dropdown');
$wa->useScript('bootstrap.collapse');
$wa->useScript('table.columns');
$wa->useScript('form.validate');
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->registerAndUseStyle('sdajem', 'com_sdajem/sdajem.css');

$canChange = true;
$canDo = ContentHelper::getActions('com_sdajem');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = 'index.php?option=com_sdajem&task=locations.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
}

$params = $this->get('State')->get('params');

/* @var LocationsItemModel $item */
?>

<div class="sdajem_content_container">
	<form action="<?php echo Route::_('index.php?view=locations'); ?>" method="post" name="adminForm" id="adminForm">
		<div>
            <div class="mb-2">
                <div class="sda_button_group sda_button_group_right" role="group" aria-label="basic group">
                <?php if ($canDo->get('core.create')) : ?>

					<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('location.add')">
						<span class="fas fa-plus-circle" aria-hidden="true"></span>
						<?php echo Text::_('COM_SDAJEM_LOCATION_ADD'); ?>
					</button>

			    <?php endif; ?>
                <?php if ($canDo->get('core.delete')) : ?>

                    <button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('location.delete')">
                        <span class="fas fa-trash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SDAJEM_LOCATION_DELETE'); ?>
                    </button>
                <?php endif; ?>
                </div>
            </div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
					<?php if (empty($this->items)) : ?>
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
                                <?php if (!Factory::getApplication()->getIdentity()->guest) :?>
                                    <td style="width:1%" class="text-center d-sm-table-cell d-none">
                                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                                    </td>
                                <?php endif; ?>
								<th scope="col" style="width:1%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_LOCATION', 'a.title', $listDirn, $listOrder); ?>
								</th>
                                <th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SDAJEM_TABLE_TABLEHEAD_POSTALCODE', 'a.postalCode', $listDirn, $listOrder); ?>
                                </th>
							</tr>
							</thead>
							<tbody>
							<?php
							$n = count($this->items);
							foreach ($this->items as $i => $item) :
								?>
								<tr class="row<?php echo $i % 2; ?>">
                                    <?php if (!Factory::getApplication()->getIdentity()->guest) :?>
                                        <td class="text-center d-sm-table-cell d-none">
                                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        </td>
                                    <?php endif; ?>
									<th scope="row" class="has-context col-4">
										<div>
											<a href="<?php echo Route::_('index.php?option=com_sdajem&view=location&id=' . (int) $item->id); ?>">
												<?php echo $this->escape($item->title); ?>
											</a>
										</div>
									</th>
                                    <td class="d-md-table-cell">
										<?php echo $item->postalCode; ?>
                                    </td>
									<td class="small d-none d-md-table-cell">
										<?php if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->created_by == Factory::getApplication()->getIdentity()->id)): ?>
											<div class="icons">
												<?php echo HTMLHelper::_('sdajemIcon.editLocation', $item, $params); ?>
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
