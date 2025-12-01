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
use Joomla\CMS\Router\Route;
use Sda\Component\Sdajem\Site\View\Event\HtmlView;

/** @var HtmlView $this */

$comments = $this->getComments();

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('form.validate');

$canDo   = ContentHelper::getActions('com_sdajem', 'event');
$user = Factory::getApplication()->getIdentity();
$tparams = $this->getParams();

$event = $this->getItem();

?>
<div id="comments" class="row">

    <button class="button btn-primary col-auto" type="button"
            data-close-on-message
            data-reload-on-close
            data-joomla-dialog='{"popupType": "iframe","width":"90vw", "height": "90vh", "src":"index.php?option=com_sdajem&tmpl=component&task=comment.addCommentToEvent&view=modal&eventId=<?php echo $event->id; ?>&callContext=event.comment"}'>
        <?php echo Text::_('COM_SDAJEM_NEW_COMMENT'); ?>
    </button>

	<?php if($comments->count() > 0): ?>
	<?php foreach ($comments as $comment) : ?>

		<div class="row justify-content-between sda_comment_row">

            <?php if ($tparams->get('sda_avatar_field_name') && $tparams->get('sda_use_avatar')): ?>
			<div class="col-auto d-none d-sm-block align-items-start" style="width: 100px;">

						<?php
						if (!is_null($comment->commentUser->userData[$tparams->get('sda_avatar_field_name')]))
						{
							echo $comment->commentUser->userData[$tparams->get('sda_avatar_field_name')]->value;
						}
						?>

			</div>
            <?php endif;?>

			<div class="col row">
                <div class="col">
                    <div class="row sda_comment_user">
                        <?php echo HTMLHelper::date($comment->timestamp,'d.m.Y H:i:s') . ' - ' . $comment->commentUser->user->username; ?>
                    </div>
                    <div class="row">
                        <?php echo $comment->comment; ?>
                    </div>
                </div>
                <?php if (($canDo->get('core.edit.own') && $comment->users_user_id == $user->id) || $canDo->get('core.delete')): ?>

                <div class="col-auto">
                    <form action="<?php echo Route::_('index.php?option=com_sdajem'); ?>" method="post" name="commentForm<?php echo $comment->id; ?>" id="commentForm<?php echo $comment->id; ?>">
                        <button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('comment.delete', 'commentForm<?php echo $comment->id; ?>')">
                            <span class="fas fa-trash" aria-hidden="true"></span>
                        </button>

                        <input type="hidden" name="task" value=""/>
                        <input type="hidden" name="cid" value="<?php echo $comment->id; ?>"/>
                        <input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
                        <input type="hidden" name="callContext" value="event.comment"/>
                        <?php echo HTMLHelper::_('form.token'); ?>

                    </form>
                </div>

                <?php endif;?>
			</div>

		</div>

	<?php endforeach; ?>
	<?php endif; ?>

</div>
