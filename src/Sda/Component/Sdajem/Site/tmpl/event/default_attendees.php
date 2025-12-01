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
use Sda\Component\Sdajem\Site\Helper\EventHtmlHelper;
use Sda\Component\Sdajem\Site\Model\EventAttendeeModel;
use Sda\Component\Sdajem\Site\View\Event\HtmlView;

/** @var HtmlView $this */

$interests = $this->getInterests();

$wa = $this->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_sdajem');
$wa->registerAndUseScript('com_sdajem.checkbox', 'com_sdajem/checkbox.js');

$canDo   = ContentHelper::getActions('com_sdajem', 'attendings');
$user = Factory::getApplication()->getIdentity();
$tparams = $this->getParams();
$tparams->set('callContext', 'event.attending');

$event = $this->getItem();

if (isset($event->registerUntil))
{
    $event->registerUntil = date('Y-m-d', strtotime($event->registerUntil));
    $now = date('Y-m-d');
	$canRegister = $event->registerUntil >= $now;
} else {
    $canRegister = true;
}
?>
<div id="attendings" class="sda_row">
            <h5><?php echo Text::_('COM_SDAJEM_ATTENDESS'); ?></h5>
<div class="sda_row">
	<?php if (isset($interests)) : ?>
		<?php if (!$user->guest) : ?>
			<div class="sda_attendee_container">
				<?php foreach ($interests as $i => $attending) : ?>
					<?php if ($tparams->get('sda_avatar_field_name') && $tparams->get('sda_use_avatar')): ?>
						<?php EventHtmlHelper::renderAttendee(new EventAttendeeModel($attending), $tparams->get('sda_avatar_field_name')); ?>
					<?php else: ?>
						<?php EventHtmlHelper::renderAttendee(new EventAttendeeModel($attending)); ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php if($canRegister) :?>
    <div class="sda_row">
        <?= HTMLHelper::_('form.token'); ?>
        <?php echo HTMLHelper::_('sdajemIcon.register', $event, $this->getUserFittings(), $tparams); ?>
    </div>
<?php endif; ?>
</div>
