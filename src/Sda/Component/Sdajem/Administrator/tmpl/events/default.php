<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_sdajem
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;

/** @var \Sda\Component\Sdajem\Administrator\View\Events\HtmlView $this */

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns');
$canChange = true;

$listOrder = $this->escape($this->getState()->get('list.ordering'));
$listDirn  = $this->escape($this->getState()->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder && !empty($items))
{
	$saveOrderingUrl = 'index.php?option=com_sdajem&task=events.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
}

$params = ComponentHelper::getParams('com_sdajem');

$items = $this->getItems();
?>
<form action="<?php
echo Route::_('index.php?option=com_sdajem'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<?php
				if (empty($items)): ?>
					<div class="alert alert-warning">
						<?php
						echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php
				else : ?>
					<table class="table" id="eventList">
						<caption class="visually-hidden">
							<?php
							echo Text::_('COM_SDAJEM_TABLE_CAPTION'); ?>, <?php
							echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
						<tr>
							<td style="width:1%" class="text-center">
								<?php
								echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" style="width:10%" class="text-start d-md-table-cell">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'COM_SDAJEM_TABLE_TABLEHEAD_NAME',
										'a.title',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th scope="col" class="d-md-table-cell text-start">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'COM_SDAJEM_TABLE_TABLEHEAD_DATE',
										'a.startDateTime',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th scope="col" class="d-md-table-cell text-start">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'COM_SDAJEM_TABLE_TABLEHEAD_LOCATION',
										'location_name',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th scope="col" class="d-md-table-cell text-start">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'COM_SDAJEM_TABLE_TABLEHEAD_STATUS',
										'a.eventStatus',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th class="d-none d-md-table-cell">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'COM_SDAJEM_TABLEHEAD_EVENTS_ATTENDEE_COUNT',
										'attendeeCount',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th scope="col" style="width:1%; min-width:85px" class="text-center">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'JSTATUS',
										'a.published',
										$listDirn,
										$listOrder
								); ?>
							</th>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php
								echo HTMLHelper::_(
										'searchtools.sort',
										'JGRID_HEADING_ACCESS',
										'access_level',
										$listDirn,
										$listOrder
								); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php
						$n = count($items);
						foreach ($items as $i => $item) :
							?>
							<tr class="row<?php
							echo $i % 2; ?>">
								<td class="text-center">
									<?php
									echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<th scope="row" class="has-context">
									<?php
									$editIcon = '<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span>'; ?>
									<a class="hasTooltip" href="<?php
									echo Route::_(
											'index.php?option=com_sdajem&task=event.edit&id=' . (int) $item->id
									); ?>" title="<?php
									echo Text::_('JACTION_EDIT'); ?> <?php
									echo $this->escape(addslashes($item->title)); ?>">
										<?php
										echo $editIcon; ?><?php
										echo $this->escape($item->title); ?></a>
								</th>
								<td>
									<?php echo $item->getStart() . ' - ' . $item->getEnd(); ?>
								</td>
								<td>
									<?php
									echo $item->locationName; ?>
								</td>
								<td>
									<?php
									echo Text::_(EventStatusEnum::from($item->eventStatus)->getStatusLabel()); ?>
								</td>
								<td class="d-none d-md-table-cell">
									<?php
									echo $item->attendeeCount; ?>
								</td>
								<td class="text-center">
									<?php
									echo HTMLHelper::_(
											'jgrid.published',
											$item->published,
											$i,
											'events.',
											$canChange,
											'cb',
											$item->publish_up,
											$item->publish_down
									);
									?>
								</td>
								<td class="small d-none d-md-table-cell">
									<?php
									echo $item->accessLevel; ?>
								</td>

							</tr>
						<?php
						endforeach; ?>
						</tbody>
					</table>

				<?php
				endif; ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php
				echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
