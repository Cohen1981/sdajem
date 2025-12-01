<?php
/**
 * @package     Sda\Component\Sdajem\Site
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Sda\Component\Sdajem\Site\Model\UserModel;

defined('_JEXEC') or die();

/** @var \Sda\Component\Sdajem\Site\View\Location\HtmlView $this */

$wa=$this->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('sdajem', 'com_sdajem/sdajem.css');

$item = $this->getItem();

$canDo   = ContentHelper::getActions('com_sdajem', 'location');
$user = Factory::getApplication()->getIdentity();

try
{
	$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->created_by == $user->id);
}
catch (Exception $e)
{
    $canEdit = false;
}

$params = $item->params;

if ($item->latlng)
{
	$lAddressString = urlencode($item->latlng);
}
else
{
	$lAddressString = urlencode($item->street) . "+" . urlencode($item->postalCode) . "+" . urlencode($item->city);
}

$uAdressString = null;

if (!$user->guest)
{
	$userModel = new UserModel($user->id);
    $uAdressString = '';

    if (isset($userModel->profile['address1']))
    {
        $uAdressString = $uAdressString . urlencode($userModel->profile['address1']);
    }

    if (isset($userModel->profile['postal_code']))
    {
        $uAdressString = $uAdressString . '+' .urlencode($userModel->profile['postal_code']);
    }

    if (isset($userModel->profile['city']))
    {
        $uAdressString = $uAdressString . '+' . urlencode($userModel->profile['city']);
    }
}

?>

<div class="clearfix">
	<?php if (!empty($canEdit))
	{
		if ($canEdit) : ?>
			<div class="icons float-end">
				<?php echo HTMLHelper::_('sdajemIcon.editLocation', $item, $params); ?>
			</div>
		<?php endif;
	} ?>
    <h1><?php echo $item->title; ?></h1>
    <p><?php echo $item->street; ?></p>
    <p><?php echo $item->postalCode; ?></p>
    <p><?php echo $item->city; ?></p>
    <p><?php echo $item->description; ?></p>
    <p><?php echo $item->latlng; ?></p>
	<p>
		<a href="https://www.google.de/maps?q=<?php echo $lAddressString; ?>"
		   class="me-2" target="_blank">
			<i class="fas fa-map-marked-alt" aria-hidden="true"></i>
			<?php echo Text::_('COM_SDAJEM_LOCATION_SHOW_ON_MAP'); ?>
		</a>

		<?php if ($uAdressString): ?>
			<a href="https://www.google.de/maps?saddr=<?php echo $uAdressString; ?>&daddr=<?php echo $lAddressString; ?>"
			   class="me-2" target="_blank">
				<i class="fas fa-route" aria-hidden="true"></i>
				<?php echo Text::_('COM_SDAJEM_LOCATION_SHOW_ROUTE'); ?>
			</a>
		<?php endif; ?>
	</p>

	<?php if ($item->image): ?>
		<div class="sdajem_teaser_image">
			<?php echo HTMLHelper::image($item->image,'',['class'=>'float-start pe-2']); ?>
		</div>
	<?php endif; ?>
	<div class="loc_address">
		<p><?php echo $item->street; ?></p>
		<p><?php echo $item->postalCode; ?></p>
		<p><?php echo $item->city; ?></p>
	</div>
</div>
