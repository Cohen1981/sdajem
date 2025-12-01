<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Sda\Component\Sdajem\Administrator\View\Location\HtmlView $this */

$app   = Factory::getApplication();
$input = $app->input;

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');

$item = $this->getItem();

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>

<form action="<?php
echo Route::_('index.php?option=com_sdajem&layout=' . $layout . $tmpl . '&id=' . (int) $item->id); ?>"
	  method="post" name="adminForm" id="location-form" class="form-validate">
	<div class="row title-alias form-vertical mb-3">
		<div class="col-12 col-md-6">
			<?php
			echo $this->getForm()->renderField('title'); ?>
		</div>
		<div class="col-12 col-md-6">
			<?php
			echo $this->getForm()->renderField('alias'); ?>
		</div>
	</div>
	<div class="main-card">
		<?php
		echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

		<?php
		echo HTMLHelper::_(
				'uitab.addTab',
				'myTab',
				'details',
				empty($item->id) ? Text::_('COM_SDAJEM_NEW_EVENT') : Text::_('COM_SDAJEM_EDIT_EVENT')
		); ?>
		<div class="row">
			<div class="col-lg-9">
				<?php
				echo $this->getForm()->renderField('description'); ?>
				<?php
				echo $this->getForm()->renderField('image'); ?>
				<?php
				echo $this->getForm()->renderField('url'); ?>
				<?php
				echo $this->getForm()->renderField('street'); ?>
				<?php
				echo $this->getForm()->renderField('postalCode'); ?>
				<?php
				echo $this->getForm()->renderField('city'); ?>
				<?php
				echo $this->getForm()->renderField('stateAddress'); ?>
				<?php
				echo $this->getForm()->renderField('country'); ?>
				<?php
				echo $this->getForm()->renderField('latlng'); ?>
			</div>
			<div class="col-lg-3">
				<?php
				echo LayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php
		echo HTMLHelper::_('uitab.endTab'); ?>

		<?php
		echo LayoutHelper::render('joomla.edit.params', $this); ?>

		<?php
		echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
		<div class="row">
			<div class="col-md-6">
				<fieldset id="fieldset-publishingdata" class="options-form">
					<legend><?php
						echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
					<div>
						<?php
						echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
					</div>
				</fieldset>
			</div>
		</div>
		<?php
		echo HTMLHelper::_('uitab.endTab'); ?>

		<?php
		echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php
	echo HTMLHelper::_('form.token'); ?>
</form>
