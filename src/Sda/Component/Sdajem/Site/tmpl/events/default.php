<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sdajem
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Site\Model\AttendingModel;
use Sda\Component\Sdajem\Site\View\Events\HtmlView;

/** @var HtmlView $this */

$items = $this->getItems();
$state = $this->getState();
/* @var WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('bootstrap.dropdown');
$wa->useScript('bootstrap.collapse');
$wa->useScript('table.columns');
$wa->useScript('form.validate');
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->registerAndUseStyle('sdajem', 'com_sdajem/sdajem.css');

$canChange = true;
$canDo     = ContentHelper::getActions('com_sdajem');

$listOrder = $this->escape($state->get('list.ordering'));
$listDirn  = $this->escape($state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder && !empty($items))
{
	$saveOrderingUrl = 'index.php?option=com_sdajem&task=events.saveOrderAjax&tmpl=component&' . Session::getFormToken(
			) . '=1';
}

$params = ComponentHelper::getParams('com_sdajem');
$currentUser = Factory::getApplication()->getIdentity();

$userAuthorizedViewLevels = $currentUser->getAuthorisedViewLevels();

?>
<div class="sdajem_content_container">

	<div class="accordion" id="accordionEvents">

		<form action="<?php
		echo Route::_('index.php?view=events'); ?>" method="post" name="adminForm" id="adminForm">
			<div>
				<div class="accordion-item">
					<h5 class="accordion-header" id="headingControls">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
								data-bs-target="#collapseControls" aria-expanded="true"
								aria-controls="collapseControls">
							<h5><?php
								echo Text::_('COM_SDAJEM_CONTROLS'); ?></h5>
						</button>
					</h5>

					<div id="collapseControls" class="accordion-collapse collapse" aria-labelledby="headingControls"
						 data-bs-parent="#accordionEvents">
						<div class="accordion-body clearfix">
							<?php
							if ($canDo->get('core.create')) : ?>

								<div class="mb-2">
									<div class="btn-group sda_button_spacer" role="group" aria-label="basic group">
										<button type="button" class="btn btn-primary"
												onclick="Joomla.submitbutton('event.add')">
											<span class="fas fa-plus-circle" aria-hidden="true"></span>
											<?php
											echo Text::_('COM_SDAJEM_EVENT_ADD'); ?>
											<span class="fa fa-calendar-plus-o" aria-hidden="true"></span>
										</button>

										<?php
										if ($params->get('sda_events_new_location')) : ?>
											<button type="button" class="btn btn-secondary"
													onclick="Joomla.submitbutton('location.add')">
												<span class="fas fa-plus-circle-o" aria-hidden="true"></span>
												<?php
												echo Text::_('COM_SDAJEM_LOCATION_ADD'); ?>
												<span class="fas fa-map-marker-alt" aria-hidden="true"></span>
											</button>
										<?php
										endif; ?>
									</div>

									<?php
									if ($canDo->get('core.delete')) : ?>
										<div class="btn-group sda_button_spacer d-sm-inline-block d-none" role="group"
											 aria-label="Attending group">
											<button type="button" class="btn btn-danger"
													onclick="Joomla.submitbutton('event.delete')">
												<span class="fas fa-trash" aria-hidden="true"></span>
												<?php
												echo Text::_('COM_SDAJEM_EVENT_DELETE'); ?>
												<span class="fa fa-calendar-minus-o" aria-hidden="true"></span>
											</button>
										</div>
									<?php
									endif; ?>

									<?php
									if ($params->get('sda_use_attending'))
									{
										echo '<div class="btn-group sda_button_spacer d-sm-inline-block d-none" role="group" aria-label="Attending group">';
										foreach (IntAttStatusEnum::cases() as $stat)
										{
											if ($stat != IntAttStatusEnum::NA)
											{
												$text = '<button type="button" class="btn ' . $stat->getButtonClass(
														) . '" onclick="Joomla.submitbutton(\'' . $stat->getAction(
														) . '\')">'
														. '<span class="icon-spacer ' . $stat->getIcon(
														) . '" aria-hidden="true"></span>';
												$text .= Text::_($stat->getButtonLabel()) . '</button>';
												echo $text;
											}
										}
										echo '</div>';
									}
									?>
								</div>

							<?php
							endif; ?>
							<div class="row justify-content-between">
								<div class="col">
									<form action="<?php
									echo Route::_('index.php?view=events'); ?>" method="post" name="adminForm1"
										  id="adminForm1">
										<?php
										echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row align-items-end">
				<?php
				$checked = ($currentUser->getParam('events_tpl', 'default') === 'default') ? '' : 'checked';
				?>
				<form action="<?php
				echo Route::_('index.php?view=events'); ?>" method="post" name="tplForm" id="tplForm">
					<div class="col"></div>
					<div class="form-check form-switch col-auto">
						<input class="form-check-input" type="checkbox" value="" id="default_tpl" switch
							   onclick="Joomla.submitbutton('event.changeTpl', 'tplForm')" <?php
						echo $checked; ?>
						<label class="form-check-label" for="default_tpl">
							<?php
							echo Text::_('COM_SDAJEM_EVENTS_TPL_CARDS'); ?>
						</label>
					</div>
					<input type="hidden" name="task" value/>
					<input type="hidden" name="return" value="<?php
					echo $this->return_page; ?>"/>
					<?php
					echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">

						<?php
						if (empty($items)) : ?>
							<div class="alert alert-warning">
								<?php
								echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
							</div>
						<?php
						else : ?>
							<table class="table table-striped" id="eventList">
								<caption class="visually-hidden">
									<?php
									echo Text::_('COM_SDAJEM_TABLE_CAPTION'); ?>, <?php
									echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
								<tr>
									<?php
									if (!$currentUser->guest) : ?>
										<td style="width:1%" class="text-center d-sm-table-cell d-none">
											<?php
											echo HTMLHelper::_('grid.checkall'); ?>
										</td>
									<?php
									endif; ?>
									<th scope="col" style="width:1%" class=" d-sm-table-cell d-none">
										<?php
										echo HTMLHelper::_(
												'searchtools.sort',
												'COM_SDAJEM_TABLE_TABLEHEAD_NAME',
												'a.title',
												$listDirn,
												$listOrder
										); ?>
									</th>
									<th scope="col" style="width:10%" class="d-none d-sm-table-cell">
										<?php
										echo HTMLHelper::_(
												'searchtools.sort',
												'COM_SDAJEM_TABLE_TABLEHEAD_DATE',
												'a.startDateTime',
												$listDirn,
												$listOrder
										); ?>
									</th>
									<?php
									if (!$currentUser->guest) : ?>
										<th class="d-none d-sm-table-cell">
											<?php
											echo HTMLHelper::_(
													'searchtools.sort',
													'COM_SDAJEM_TABLEHEAD_EVENTS_STATUS',
													'a.eventStatus',
													$listDirn,
													$listOrder
											); ?>
										</th>
									<?php
									endif; ?>
								</tr>
								</thead>

								<tbody>
								<?php
								$n = count($items);
								foreach ($items as $i => $item) : ?>
									<?php
									if ($item->eventStatusEnum == EventStatusEnum::CONFIRMED->value || in_array(
													$params->get('sda_public_planing'),
													$userAuthorizedViewLevels
											)) : ?>
										<tr class="row<?php
										echo $i % 2; ?>">
											<?php
											if (!$currentUser->guest) : ?>
												<td class="text-center d-sm-table-cell d-none">
													<?php
													echo HTMLHelper::_('grid.id', $i, $item->id); ?>
												</td>
											<?php
											endif; ?>
											<th scope="row" class="has-context col-4">
												<div>
													<a href="<?php
													echo Route::_(
															'?option=com_sdajem&view=event&id=' . (int) $item->id
													); ?>">
														<?php
														echo $this->escape($item->title); ?>
													</a>
												</div>
												<?php
												if ($params->get('sda_use_location')) : ?>
													<div class="small">
														<?php
														echo Text::_('COM_SDAJEM_LOCATION') . ': ' . $this->escape(
																		$item->locationName
																); ?>
													</div>
												<?php
												endif; ?>
												<?php
												if ($params->get('sda_use_attending') && !$currentUser->guest) : ?>

													<?php
													$attStatus = (AttendingModel::getAttendingToEvent($currentUser->id, $item->id));
													?>
													<div class="small">

														<?php
														if ($item->eventStatusEnum == EventStatusEnum::PLANING->value) : ?>

															<?php
															echo $attStatus->statusEnum->getInterestStatusBadge(); ?>

														<?php
														else: ?>

															<?php
															if ($attStatus->event_status === EventStatusEnum::PLANING)
															{
																echo $attStatus->statusEnum->getInterestStatusBadge() . ' ';
															}
															else
															{
																echo $attStatus->statusEnum->getAttendingStatusBadge();
															}
															?>

														<?php
														endif; ?>

													</div>

												<?php
												endif; ?>
											</th>
											<td class="col-3">
												<div>
													<?php
													if ($item->allDayEvent)
													{
														echo HTMLHelper::date($item->startDateTime, 'd.m.Y', true);
														echo ' - ';
														echo HTMLHelper::date($item->endDateTime, 'd.m.Y', true);
													}
													else
													{
														echo HTMLHelper::date($item->startDateTime, 'd.m.Y H:i', true);
														echo ' - ';
														echo HTMLHelper::date($item->endDateTime, 'd.m.Y H:i', true);
													}
													?>
												</div>
												<div>
													<?php
													if (!$currentUser->guest)
													{
														if ($item->eventStatusEnum <> EventStatusEnum::PLANING->value)
														{
															echo $item->attendeeFeedbackCount . ' ' . Text::_(
																			'COM_SDAJEM_FEEDBACK_COUNT'
																	) . '</br>';
															echo $item->attendeeCount . ' ' . Text::_(
																			'COM_SDAJEM_ATTENDEE_COUNT'
																	) . ' / ' . $item->guestCount . ' ' . Text::_(
																			'COM_SDAJEM_GUEST_COUNT'
																	);
														}
														else
														{
															echo $item->feedbackCount . ' ' . Text::_(
																			'COM_SDAJEM_FEEDBACK_COUNT'
																	) . '</br>';
															if ($canDo->get('core.manage') ||
																	($canDo->get(
																					'core.edit.own'
																			) && $item->created_by == $currentUser->id)
															)
															{
																echo $item->interestCount . ' ' . Text::_(
																				'COM_SDAJEM_INTEREST_COUNT'
																		) . ' / ' . $item->guestCount . ' ' . Text::_(
																				'COM_SDAJEM_GUEST_COUNT'
																		);
															}
														}
													}
													?>
												</div>
											</td>
											<?php
											if (!$currentUser->guest) : ?>
												<td class="d-sm-table-cell d-none">
													<div>
														<?php
														if ($item->registerUntil):
															echo Text::_('COM_SDAJEM_REGISTER_UNTIL') . ': <br>';
															echo HTMLHelper::date($item->registerUntil, 'd.m.Y', true);
														endif ?>
													</div>
													<?php
													if ($item->organizerName): ?>
														<div><?php
															echo Text::_(
																			'COM_SDAJEM_ORGANIZIG'
																	) . ': ' . $item->organizerName ?></div>
													<?php
													endif; ?>
													<?php
													if ($currentUser->id == $item->organizerId || $canDo->get(
																	'core.manage'
															)) : ?>
														<div class="sda_form">
															<?php
															$class = $item->eventStatusEnum->getStatusColorClass();
															?>
															<button type="button" class="btn btn-sm <?php
															echo $class; ?> dropdown-toggle" data-bs-toggle="dropdown"
																	aria-expanded="false">
																<?php
																echo Text::_($item->eventStatusEnum->getStatusLabel()); ?>
															</button>
															<ul class="dropdown-menu">

																<?php
																foreach (EventStatusEnum::cases() as $status) : ?>
																	<?php
																	if ($status != $item->eventStatusEnum) : ?>
																		<li><?php
																			echo HTMLHelper::_(
																					'sdajemIcon.switchEventStatus',
																					$item,
																					$status
																			); ?></li>
																	<?php
																	endif; ?>
																<?php
																endforeach; ?>
															</ul>
														</div>
													<?php
													else: ?>
														<div>
															<?php
															echo $item->eventStatusEnum->getStatusBadge(); ?>
														</div>
													<?php
													endif; ?>
												</td>
											<?php
											endif; ?>
											<td class="small d-none d-md-table-cell">
												<?php
												if ($canDo->get('core.edit') ||
														($canDo->get(
																		'core.edit.own'
																) && $item->created_by == $currentUser->id)
												) : ?>

													<div class="icons">
														<?php
														echo HTMLHelper::_('sdajemIcon.edit', $item, $params); ?>
													</div>

												<?php
												endif; ?>
											</td>
										</tr>
									<?php
									endif; ?>
								<?php
								endforeach; ?>
								</tbody>
							</table>

							<?php
							echo $this->pagination->getListFooter(); ?>

						<?php
						endif; ?>
					</div>
				</div>
			</div>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="return" value="<?php
			echo $this->return_page; ?>"/>
			<?php
			echo HTMLHelper::_('form.token'); ?>

		</form>

	</div>
</div>
