<?php

namespace Sda\Component\Sdajem\Site\Dispatcher;

use Joomla\CMS\Dispatcher\ComponentDispatcher;

class Dispatcher extends ComponentDispatcher
{
	protected function loadLanguage()
	{
		$this->app->getLanguage()->load('com_sdajem', JPATH_ADMINISTRATOR . '/components/com_sdajem');

		parent::loadLanguage();
	}
}