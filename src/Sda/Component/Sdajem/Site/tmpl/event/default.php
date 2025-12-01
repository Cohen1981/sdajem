<?php
/**
 * @package     Sda\Component\Sdajem\Site
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Site\View\Event\HtmlView;

/** @var HtmlView $this */

$wa = $this->getDocument()->getWebAssetManager();

$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->useStyle('com_sdajem.sdajem');
$wa->useScript('com_sdajem.checkbox');

$wa->useScript('bootstrap.dropdown');
$wa->useScript('bootstrap.collapse');
$wa->useScript('joomla.dialog-autocreate');

$canDo   = ContentHelper::getActions('com_sdajem', 'event');
$user = Factory::getApplication()->getIdentity();

$event = $this->getItem();

$canEdit = false;

try
{
    $canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $event->created_by == $user->id);
}
catch (Exception $e)
{
}

$tparams = $event->paramsRegistry;

$organizer = (($this->getOrganizer()) ?? null);

$host = (($this->getHost()) ?? null);

$uAdressString = null;

$currentUser = Factory::getApplication()->getIdentity();

?>
<div class="sdajem_content_container">

    <!-- Event head -->
    <div class="sda_row">
        <div class="sda_event_head">
            <div>
                <h3>
                    <?php echo $this->escape($event->title);?>
                </h3>
                <h4>
                    <?php
                    echo $event->getStart();
                    echo ' - ';
                    echo $event->getEnd();
                    ?>
                </h4>
                <?php if (!$currentUser->guest) : ?>
                <h5>
	                <?php if ($currentUser->id == $event->organizerId || $canDo->get('core.manage')) : ?>

		                <?php
		                $class = $event->eventStatusEnum->getStatusColorClass();
		                ?>
                        <button type="button" class="btn btn-sm <?php echo $class; ?> dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			                <?php echo Text::_($event->eventStatusEnum->getStatusLabel()); ?>
                        </button>
                        <ul class="dropdown-menu">

			                <?php foreach (EventStatusEnum::cases() as $status) : ?>
				                <?php if ($status != $event->eventStatus) : ?>
                                    <li><?php echo HTMLHelper::_('sdajemIcon.switchEventStatus',$event, $status); ?></li>
				                <?php endif; ?>
			                <?php endforeach; ?>
                        </ul>

	                <?php else: ?>

		                <?php echo $event->eventStatusEnum->getStatusBadge(); ?>

	                <?php endif; ?>
                </h5>
                <?php endif; ?>
	            <?php if($tparams->get('sda_use_organizer') && isset($organizer) && !$user->guest) : ?>
                    <h6><?php echo Text::_('COM_SDAJEM_FIELD_ORGANIZER_LABEL'); ?>: <?php echo $organizer->user->name; ?></h6>
	            <?php endif; ?>
            </div>
            <?php if ($canEdit) : ?>
                <div class="icons">
                    <?php echo HTMLHelper::_('sdajemIcon.edit', $event, $tparams); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="sda_row clearfix">
        <?php if ($event->image) : ?>
            <div class="sdajem_teaser_image">
                <?php echo HTMLHelper::image($event->image,'',['class'=>'float-start pe-2']); ?>
            </div>
        <?php endif; ?>
        <?php if ($event->url) : ?>
            <a href="<?php echo $event->url; ?>" target="_blank"><?php echo Text::_('COM_SDAJEM_EVENT_URL'); ?></a>
        <?php endif; ?>
        <p><?php echo $event->description; ?></p>
    </div>

    <!-- Accordions start -->
    <div class="accordion" id="accordionEvent">

        <!-- Location start -->
	    <?php if (isset($event->sdajem_location_id)) : ?>
            <?php
            $status = ($this->activeAccordion === 'event.location') ? 'show' :  '';
            $collapsed = ($this->activeAccordion === 'event.location') ? '' : 'collapsed';
            ?>
        <div class="accordion-item">
            <div class="accordion-header" id="headingLocation">
                <button class="accordion-button <?php echo $collapsed; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLocation" aria-expanded="true" aria-controls="collapseLocation">
                    <h5>
                    <?php
                        echo Text::_('COM_SDAJEM_LOCATION') . ': ';
                        echo $event->locationName;
                        ?>
                    </h5>
                </button>
            </div>

            <div id="collapseLocation" class="accordion-collapse collapse <?php echo $status; ?>" aria-labelledby="headingLocation" data-bs-parent="#accordionEvent">

                <?php
                echo $this->loadTemplate('location');
                ?>

            </div>
        </div>
	    <?php endif; ?>
        <!-- Location end -->

	    <?php if(isset($event->hostId)) : ?>
            <?php
            $status = ($this->activeAccordion === 'event.host') ? 'show' :  '';
            $collapsed = ($this->activeAccordion === 'event.host') ? '' : 'collapsed';
            ?>
            <div class="accordion-item">
                <div class="accordion-header" id="headingHost">
                    <button class="accordion-button <?php echo $collapsed; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHost" aria-expanded="true" aria-controls="collapseHost">
                        <h5><?php echo Text::_('COM_SDAJEM_FIELD_HOST_LABEL'); ?>: <?php echo $host->getName(); ?></h5>
                    </button>
                </div>
                <div id="collapseHost" class="accordion-collapse collapse <?php echo $status; ?>" aria-labelledby="headingHost" data-bs-parent="#accordionEvent">
                    <div class="accordion-body">
	                    <?php if ($canEdit) : ?>
                            <div class="icons float-end">
                                <?php echo HTMLHelper::_('contacticon.edit', $host, $tparams); ?>
                            </div>
                        <?php endif;?>
                        <p><?php echo $host->get('telephone'); ?></p>
                        <p><?php echo $host->get('email_to'); ?></p>
                        <p><?php echo $host->get('mobile'); ?></p>
                    </div>
                </div>
            </div>
	    <?php endif; ?>

        <?php if ($tparams->get('sda_use_attending') && !$user->guest): ?>
            <?php
            $status = ($this->activeAccordion === 'event.attending') ? 'show' :  '';
            $collapsed = ($this->activeAccordion === 'event.attending') ? '' : 'collapsed';
            ?>
        <div class="accordion-item">
            <div class="accordion-header" id="headingAttendings">
                <button class="accordion-button <?php echo $collapsed; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttendings" aria-expanded="true" aria-controls="collapseAttendings">
                    <h5><?php echo Text::_('COM_SDAJEM_FIELD_ATTENDINGS_LABEL'); ?>&nbsp;</h5>
                    <?php
                    if ($event->registerUntil)
                        echo ' (' . Text::_('COM_SDAJEM_FIELD_ATTENDINGS_UNTIL') . ' ' . HTMLHelper::date($event->registerUntil,'d.m.Y') . ') <br>';
                    ?>
                </button>
            </div>
            <div id="collapseAttendings" class="accordion-collapse collapse <?php echo $status; ?>" aria-labelledby="headingAttendings" data-bs-parent="#accordionEvent">
                <div class="accordion-body">
                    <?php
                        echo $this->loadTemplate('attendees');
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

	    <?php if ($tparams->get('sda_events_use_fittings') && !$user->guest && $this->getEventFittings()->count() > 0): ?>
            <?php
            $status = ($this->activeAccordion === 'event.planing') ? 'show' :  '';
            $collapsed = ($this->activeAccordion === 'event.planing') ? '' : 'collapsed';
            ?>

        <div class="accordion-item d-none d-sm-block">
            <div class="accordion-header" id="headingPlaningArea">
                <button class="accordion-button <?php echo $collapsed; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePlaningArea" aria-expanded="true" aria-controls="collapsePlaningArea">
                    <h5><?php echo Text::_('COM_SDAJEM_PLANING_AREA_LABEL'); ?></h5>
                </button>
            </div>
            <div id="collapsePlaningArea" class="accordion-collapse collapse <?php echo $status; ?>" aria-labelledby="headingPlaningArea" data-bs-parent="#accordionEvent">
                <div class="accordion-body">

                    <?php echo $this->loadTemplate('planing'); ?>

                </div>
            </div>
        </div>
        <div class="accordion-item d-sm-none">
            <button class="accordion-button collapsed" type="button"
                    data-joomla-dialog='{"popupType": "iframe", "width":"90vw", "height": "90vh", "src":"index.php?option=com_sdajem&view=event&tmpl=component&layout=planning_modal&id=<?php echo $event->id; ?>"}'>
                <?php echo Text::_('COM_SDAJEM_PLANING_AREA_LABEL'); ?>
            </button>
        </div>

        <?php endif; ?>

	    <?php if ($tparams->get('sda_events_use_comments') && !$user->guest): ?>
            <?php
            $status = ($this->activeAccordion === 'event.comment') ? 'show' :  '';
            $collapsed = ($this->activeAccordion === 'event.comment') ? '' : 'collapsed';
            ?>
            <div class="accordion-item">
                <div class="accordion-header" id="headingCommentArea">
                    <button class="accordion-button <?php echo $collapsed; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCommentArea" aria-expanded="true" aria-controls="collapseCommentArea">
                        <h5><?php echo Text::_('COM_SDAJEM_COMMENT_AREA_LABEL') . ' '; ?></h5>&nbsp;
                        <?php echo ' (' . $this->getComments()->count() . ')'; ?>
                    </button>
                </div>
                <div id="collapseCommentArea" class="accordion-collapse collapse <?php echo $status; ?>" aria-labelledby="headingCommentArea" data-bs-parent="#accordionEvent">
                    <div class="accordion-body">

					    <?php echo $this->loadTemplate('comments'); ?>

                    </div>
                </div>
            </div>
	    <?php endif; ?>
    </div>
</div>
