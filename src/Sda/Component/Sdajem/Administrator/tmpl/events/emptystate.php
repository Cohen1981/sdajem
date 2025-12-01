<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sdajem
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_SDAJEM',
	'formURL' => 'index.php?option=com_sdajem',
	'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_sdajem') || count($user->getAuthorisedCategories('com_sdajem', 'core.create')) > 0) {
	$displayData['createURL'] = 'index.php?option=com_sdajem&task=event.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);