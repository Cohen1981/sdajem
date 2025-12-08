<?php
/**
 * @package        Sda\Component\Sdajem\Site
 * @subpackage     com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @since          1.5.3
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Site\View\Event\HtmlView;

/** @var HtmlView $this */

$canDo = ContentHelper::getActions('com_sdajem', 'event');
$user  = Factory::getApplication()->getIdentity();

$event = $this->getItem();

$canEdit = false;

try
{
    $canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $event->created_by == $user->id);
}
catch (Exception $e)
{
}

$tparams = $this->getParams();

$host = (($this->getHost()) ?? null);

?>

<div class='accordion-body'>
    <?php if ($canEdit) : ?>
        <div class="icons float-end">
            <?php echo HTMLHelper::_('contacticon.edit', $host, $tparams); ?>
        </div>
    <?php endif; ?>
    <div class="row justify-content-between">
        <div class="col-1"><span class="fa fa-globe" aria-hidden="true"></span></div>
        <div class="col-11">
            <a href="<?php echo $host->webpage; ?>" target="_blank">
                <?php echo $host->webpage; ?>
            </a>
        </div>

        <?php if (!$user->guest) : ?>
            <div class="col-12">&nbsp;</div>

            <div class="col-1"><span class='fa fa-phone' aria-hidden='true'></span></div>
            <div class="col-11"><?php echo $host->telephone; ?></div>

            <div class="col-1"><span class='fa fa-mobile' aria-hidden='true'></span></div>
            <div class="col-11"><?php echo $host->mobile; ?></div>

            <div class='col-1'><span class='fa fa-envelope' aria-hidden='true'></span></div>
            <div class='col-11'><?php echo $host->email_to; ?></div>
        <?php endif; ?>
    </div>
</div>
