<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Sda\Component\Sdajem\Administrator\View\Event\HtmlView;

/** @var HtmlView $this */

$app = Factory::getApplication();
$input = $app->input;

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('inlinehelp')
	->useScript('form.validate');

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$form = $this->getForm();
?>

<form action="<?php
echo Route::_('index.php?option=com_sdajem&layout=' . $layout . $tmpl . '&id=' . (int) $this->getItem()->id); ?>"
	  method="post" name="adminForm" id="event-form" class="form-validate">
	<div class="row title-alias form-vertical mb-3">
		<div class="col-12 col-md-6">
			<?php
			echo $form->renderField('title'); ?>
		</div>
		<div class="col-12 col-md-6">
			<?php
			echo $form->renderField('alias'); ?>
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
			empty($this->getItem()->id) ? Text::_('COM_SDAJEM_NEW_EVENT') : Text::_('COM_SDAJEM_EDIT_EVENT')
		); ?>

		<div class="row">
			<div class="col-lg-9">
				<?php
				echo $form->renderField('eventStatus'); ?>
				<?php
				echo $form->renderField('description'); ?>
				<?php
				echo $form->renderField('image'); ?>
				<?php
				echo $form->renderField('url'); ?>
				<?php
				echo $form->renderField('sdajem_location_id'); ?>
				<?php
				echo $form->renderField('allDayEvent'); ?>
				<?php
				echo $form->renderField('startDateTime'); ?>
				<?php
				echo $form->renderField('endDateTime'); ?>
				<?php
				echo $form->renderField('organizerId'); ?>
				<?php
				echo $form->renderField('hostId'); ?>
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
