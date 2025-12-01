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
use Sda\Component\Sdajem\Site\Model\UserModel;
use Sda\Component\Sdajem\Site\View\Event\HtmlView;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/** @var HtmlView $this */

$canDo   = ContentHelper::getActions('com_sdajem', 'location');
$user = Factory::getApplication()->getIdentity();
$tparams = $this->getParams();

$location = (($this->getLocation()) ?? null);

$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $location->created_by == $user->id);

$lAddressString = '';

if (!$location === null)
{
	if (isset($location->latlng))
	{
		$lAddressString = urlencode($location->latlng);
	}
	else
	{
		$lAddressString = $lAddressString . (isset($location->street)) ? urlencode($location->street) : '';
		$lAddressString = $lAddressString . '+' . (isset($location->postalCode)) ? urlencode($location->postalCode) : '';
		$lAddressString = $lAddressString . '+' . (isset($location->city)) ? urlencode($location->city) : '';
	}
}

$uAdressString = null;

if (!$user->guest)
{
	$userModel     = new UserModel($user->id);
	$uAdressString = '';

	if (isset($userModel->profile['address1']))
	{
		$uAdressString = $uAdressString . urlencode($userModel->profile['address1']);
	}

	if (isset($userModel->profile['postal_code']))
	{
		$uAdressString = $uAdressString . '+' . urlencode($userModel->profile['postal_code']);
	}

	if (isset($userModel->profile['city']))
	{
		$uAdressString = $uAdressString . '+' . urlencode($userModel->profile['city']);
	}
}
?>

<div class='accordion-body clearfix'>

	<?php if ($canEdit) : ?>
		<div class="icons float-end">
			<?php echo HTMLHelper::_('sdajemIcon.editLocation', $location, $tparams); ?>
		</div>
	<?php endif; ?>

	<?php if (isset($lAddressString)): ?>
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
	<?php endif; ?>

	<?php if ($location->image): ?>
		<div class="sdajem_teaser_image">
			<?php echo HTMLHelper::image($location->image, '', ['class' => 'float-start pe-2']); ?>
		</div>
	<?php endif; ?>
	<div class="loc_address">
		<p><?php echo $location->street; ?></p>
		<p><?php echo $location->postalCode; ?></p>
		<p><?php echo $location->city; ?></p>
	</div>
</div>
