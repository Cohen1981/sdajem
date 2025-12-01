<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/** @var \Sda\Component\Sdajem\Administrator\View\Locations\HtmlView $this */

$app = Factory::getApplication();
$wa  = $this->getDocument()->getWebAssetManager();
$wa->useScript('modal-content-select');


$function = $app->input->getCmd('function', 'jSelectLocation');
$onclick  = $this->escape($function);
?>

<div class="container-popup">
	<form action="<?php
	echo Route::_(
			'index.php?option=com_sdajem&view=locations&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken(
			) . '=1'
	); ?>"
		  method="post"
		  name="adminForm"
		  id="adminForm"
		  class="form-inline"
	>
		<?php
		if (empty($this->getItems())) : ?>
			<div class="alert alert-warning">
				<?php
				echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php
		else : ?>
			<table class="table table-sm">
				<thead>
				<tr>
					<th scope="col" style="width:10%" class="d-none d-md-table-cell">
					</th>
					<th scope="col" style="width:1%">
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$iconStates = [
						-2 => 'icon-trash',
						0  => 'icon-unpublish',
						1  => 'icon-publish',
						2  => 'icon-archive',
				];
				?>
				<?php
				foreach ($this->getItems() as $i => $item) : ?>
					<?php
					$lang    = '';
					$attribs = 'data-content-select'
							. ' data-id="' . $item->id . '"'
							. ' data-title="' . $this->escape($item->title) . '"';
					?>
					<tr class="row<?php
					echo $i % 2; ?>">
						<th scope="row">
							<a class="select-link" <?php
							echo $attribs; ?>
							   href="javascript:void(0)"
							   data-function="<?php
							   echo $this->escape($onclick); ?>"
							   data-id="<?php
							   echo $item->id; ?>"
							   data-title="<?php
							   echo $this->escape($item->title); ?>"
							>
								<?php
								echo $this->escape($item->title); ?>
							</a>
						</th>
						<td>
							<?php
							echo (int) $item->id; ?>
						</td>
					</tr>
				<?php
				endforeach; ?>
				</tbody>
			</table>
		<?php
		endif; ?>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="forcedLanguage" value="<?php
		echo $app->input->get('forcedLanguage', '', 'CMD'); ?>">
		<?php
		echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
