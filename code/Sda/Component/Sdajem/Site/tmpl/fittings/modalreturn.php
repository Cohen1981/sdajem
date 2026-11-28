<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sdajem
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var Sda\Component\Sdajem\Site\View\Fittings\HtmlView $this */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('modal-content-select');

$this->getDocument()->addScriptOptions('content-select-on-load', $this->getItems(), false);
