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
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Site\Model\AttendingModel;
use Sda\Component\Sdajem\Site\View\Events\HtmlView;

/** @var HtmlView $this */

$items = $this->getItems();
$state = $this->getState();

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
	$saveOrderingUrl = 'index.php?option=com_sdajem&task=events.saveOrderAjax&tmpl=component&'
			. Session::getFormToken() . '=1';
}

$params                   = ComponentHelper::getParams('com_sdajem');
$currentUser              = Factory::getApplication()->getIdentity();
$userAuthorizedViewLevels = $currentUser->getAuthorisedViewLevels();
?>
<div class="sdajem_content_container">

	<!--Header with general controls and search-->
	<div class="accordion" id="accordionEvents">
		<div class="accordion-item">
			<h5 class="accordion-header" id="headingControls">
				<button class="accordion-button sda_events_accordion collapsed"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#collapseControls"
						aria-expanded="true"
						aria-controls="collapseControls">
					<h5><?php
						echo Text::_('COM_SDAJEM_CONTROLS'); ?></h5>
				</button>
			</h5>

			<div id="collapseControls"
				 class="accordion-collapse collapse"
				 aria-labelledby="headingControls"
				 data-bs-parent="#accordionEvents">
				<div class="accordion-body clearfix row justify-content-between align-content-center">
					<!-- Joomla searchtools -->
					<div class="col">
						<form action="<?php
						echo Route::_('index.php?view=events'); ?>"
							  method="post"
							  name="adminForm1"
							  id="adminForm1">
							<?php
							echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
						</form>
					</div>

					<?php
					if ($canDo->get('core.delete')) : ?>
						<!-- Show the delete buttons on events  -->
						<div class="col-auto align-content-center">
							<div class="btn btn-danger"
								 type="button"
								 data-bs-toggle="collapse"
								 data-bs-target=".multi-collapse"
								 aria-expanded="false"
								 aria-controls=""
							>
								<?php
								echo Text::_('COM_SDAJEM_DELETE_TOGGLE'); ?>
							</div>
						</div>
					<?php
					endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!--New Buttons and Template switch-->
	<div class="row justify-content-between sda-padding-top-bottom">
		<!-- New buttons -->
		<?php
		if ($canDo->get('core.create')) :
			?>
			<div class="col-sm-3">
				<form action="<?php
				echo Route::_('index.php?view=events'); ?>"
					  method="post"
					  name="adminForm"
					  id="adminForm"
				>
					<button type="button"
							class="btn btn-primary"
							onclick="Joomla.submitbutton('event.add', 'adminForm')"
					>
						<span class="fas fa-plus-circle" aria-hidden="true"></span>
						<?php
						echo Text::_('COM_SDAJEM_EVENT_ADD'); ?>
						<span class="fa fa-calendar-plus-o" aria-hidden="true"></span>
					</button>
					<input type="hidden" name="task" value/>
					<input type="hidden" name="return" value="<?php
					echo $this->return_page; ?>"/>
					<?php
					echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>
		<?php
		endif; ?>

		<!-- Template switch -->
		<div class="col-auto align-content-center">
			<?php
			$checked = ($currentUser->getParam('events_tpl', 'default') === 'default') ? '' : 'checked';
			?>
			<form action="<?php
			echo Route::_('index.php?view=events'); ?>" method="post" name="tplForm" id="tplForm">
				<div class="form-check form-switch float-right">
					<input class="form-check-input"
						   type="checkbox"
						   value=""
						   id="default_tpl"
						   switch
						   onclick="Joomla.submitbutton('event.changeTpl', 'tplForm')" <?php
					echo $checked; ?>
					<label class="form-check-label" for="default_tpl">
						<?php
						echo Text::_('COM_SDAJEM_EVENTS_TPL_TABLE'); ?>
					</label>
				</div>
				<input type="hidden" name="task" value/>
				<input type="hidden" name="return" value="<?php
				echo $this->return_page; ?>"/>
				<?php
				echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>

	</div>

	<!--Events list-->
	<div class="col-md-12">
		<div id="j-main-container" class="j-main-container">
			<!-- no events -->
			<?php

			if (empty($items)) :
				?>
				<div class="alert alert-warning">
					<?php
					echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else :
				?>
				<?php
				foreach ($items as $event) :
					?>
					<!-- event cards -->
					<div class="card">
						<!-- card header -->
						<div class="card-header row justify-content-between">
							<!-- col1 title and date -->
							<div class="col">
								<a href="<?php
								echo Route::_(
										'index.php?view=event&id='
										. $event->id
								); ?>">
									<h5>
										<p>
											<?php
											echo $event->title; ?>
										</p>
										<div class="row">
											<span class="fa fa-calendar col-auto" aria-hidden="true"></span>
											<div class="col">
												<?php
													echo $event->getStart();
													echo ' - ';
													echo $event->getEnd();
												?>
											</div>
										</div>
									</h5>
								</a>
							</div>

							<!-- col2 event status and edit -->
							<?php
							if (!$currentUser->guest) :
								?>
								<div class="col-auto align-content-center">

									<!-- Event status -->
									<?php
									if ($currentUser->id == $event->organizerId || $canDo->get('core.manage')) :
										?>
										<div class="row pb-1">
											<?php
											$class = $event->eventStatusEnum->getStatusColorClass();
											?>
											<button type="button" class="btn btn-sm <?php
											echo $class; ?> dropdown-toggle" data-bs-toggle="dropdown"
													aria-expanded="false">
												<?php
												echo Text::_($event->eventStatusEnum->getStatusLabel()); ?>
											</button>
											<ul class="dropdown-menu">

												<?php
												foreach (EventStatusEnum::cases() as $status) :
													?>
													<?php
													if ($status != $event->eventStatusEnum) :
														?>
														<li><?php
															echo HTMLHelper::_(
																	'sdajemIcon.switchEventStatus',
																	$event,
																	$status
															); ?></li>
													<?php
													endif; ?>
												<?php
												endforeach; ?>
											</ul>

										</div>
									<?php
									else :
										?>
										<div class="row">
											<?php
											echo $event->eventStatusEnum->getStatusBadge(); ?>
										</div>
									<?php
									endif; ?>

									<!-- edit button -->
									<?php
									if ($canDo->get('core.edit') || ($canDo->get(
															'core.edit.own'
													) && $event->created_by == $currentUser->id)) :
										?>

										<div class="icons row">
											<?php
											echo HTMLHelper::_('sdajemIcon.edit', $event, $params); ?>
										</div>

									<?php
									endif; ?>

								</div>
							<?php
							endif; ?>
						</div>

						<!-- card body -->
						<div class="card-body row justify-content-between">
							<a class="col-sm row justify-content-between align-content-center no-decor" href="<?php
							echo Route::_('index.php?view=event&id=' . $event->id); ?>">

								<!-- register until-->
								<?php
								if ($event->registerUntil) :
									?>
									<div class="col-1">
										<span class="fa fa-calendar-times-o" aria-hidden="true">&nbsp;</span>
									</div>

									<div class="col-11">
                                        <?php
                                            echo Text::_('COM_SDAJEM_REGISTER_UNTIL') . ': ' . HTMLHelper::date(
                                                            $event->registerUntil,
                                                            'd.m.Y',
                                                            true
                                                    );
                                        ?>
									</div>
								<?php endif ?>

                                <!-- organizer -->
                                <?php if ($event->organizerName) : ?>
                                <div class="col-1">&nbsp;</div>
                                <div class="col-11">
                                    <?php echo Text::_('COM_SDAJEM_ORGANIZIG') . ': ' . $event->organizerName ?>
                                </div>
                                <?php endif ?>

								<!-- location -->
								<?php
								if ($params->get('sda_use_location')) :
									?>
									<div class="col-1">
										<span class="fas fa-map-marker-alt " aria-hidden="true"></span>
									</div>
									<div class="col-11">
										<?php
										echo ' ' . $this->escape($event->postalCode) . ' ' . $this->escape(
														$event->locationName
												); ?>
									</div>
								<?php
								endif; ?>

								<!-- feedbacks-->
								<?php
								if (!$currentUser->guest)
								{
									echo '<div class="col-1"><span class="fa fa-users" aria-hidden="true"></span>&nbsp;</div>';
									echo '<div class="col-11">';
									if ($event->eventStatusEnum <> EventStatusEnum::PLANING)
									{
										echo $event->attendeeFeedbackCount . ' ' . Text::_(
														'COM_SDAJEM_FEEDBACK_COUNT'
												) . '<br>';
										echo $event->attendeeCount . ' ' . Text::_(
														'COM_SDAJEM_ATTENDEE_COUNT'
												) . ' / ' . $event->guestCount . ' ' . Text::_(
														'COM_SDAJEM_GUEST_COUNT'
												);
									}
									else
									{
										echo $event->feedbackCount . ' ' . Text::_(
														'COM_SDAJEM_FEEDBACK_COUNT'
												) . '<br>';
										if (
												$canDo->get('core.manage') ||
												($canDo->get('core.edit.own') && $event->created_by == $currentUser->id)
										)
										{
											echo $event->interestCount . ' ' . Text::_(
															'COM_SDAJEM_INTEREST_COUNT'
													) . ' / ' . $event->guestCount . ' ' . Text::_(
															'COM_SDAJEM_GUEST_COUNT'
													);
										}
									}
									echo '</div>';
								}
								?>

								<!-- own feedback-->
								<?php
								if ($params->get('sda_use_attending') && !$currentUser->guest) :
									?>

									<div class="col-1">
										<span class="fa fa-user" aria-hidden="true"></span>&nbsp;
									</div>
									<div class="col-11">
										<?php
										$attending = AttendingModel::getAttendingToEvent($currentUser->id, $event->id);
										?>
										<div>
											<?php
											if ($event->eventStatusEnum == EventStatusEnum::PLANING->value) :
												?>

												<?php
												echo $attending->statusEnum->getInterestStatusBadge(); ?>

											<?php
											else :
												?>

												<?php
												if ($attending->event_status === EventStatusEnum::PLANING)
												{
													echo $attending->statusEnum->getInterestStatusBadge() . ' ';
												}
												else
												{
													echo $attending->statusEnum->getAttendingStatusBadge();
												}
												?>

											<?php
											endif; ?>
										</div>
									</div>
								<?php
								endif; ?>

                                <?php
                                if ($params->get('sda_events_use_comments') && !$currentUser->guest) :
                                    ?>
                                    <div class="col-1">
                                        <span class="fa fa-comments-o" aria-hidden="true"></span>&nbsp;
                                    </div>
                                    <div class="col-11">
                                        <?php
                                        echo $event->commentsCount;
                                        ?>
                                    </div>
                                <?php
                                endif; ?>

							</a>

							<?php
							if ($canDo->get('core.delete')) :
								?>
								<div class="collapse multi-collapse col-sm-auto" id="cdelete<?php
								echo $event->id; ?>">
									<form action="<?php
									echo Route::_('index.php?view=events'); ?>" method="post" name="eventForm<?php
									echo $event->id; ?>" id="eventForm<?php
									echo $event->id; ?>">

										<div class="btn btn-danger"
											 onclick="Joomla.submitbutton('event.delete','eventForm<?php
											 echo $event->id; ?>')">
											<span class="fas fa-trash" aria-hidden="true"></span>
											<?php
											echo Text::_('COM_SDAJEM_EVENT_DELETE'); ?>
											<span class="fa fa-calendar-minus-o" aria-hidden="true"></span>
										</div>

										<input type="hidden" name="id" value="<?php
										echo $event->id; ?>"/>
										<input type="hidden" name="task" value=""/>
										<input type="hidden" name="return" value="<?php
										echo $this->return_page; ?>"/>
										<?php
										echo HTMLHelper::_('form.token'); ?>
									</form>
								</div>
							<?php
							endif; ?>

						</div>
					</div>
				<?php
				endforeach; ?>
			<?php
			endif; ?>
		</div>
	</div>
	</form>
</div>
