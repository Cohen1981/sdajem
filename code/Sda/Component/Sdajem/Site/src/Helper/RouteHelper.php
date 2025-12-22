<?php

/**
 * @package     Sda\Component\Sdajem\Site\Helper
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Sda\Component\Sdajem\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Multilanguage;

abstract class RouteHelper
{
	public static function getRoute($id, string $view, $catid = 0, $language = 0)
	{
		$link = '?option=com_sdajem&view=' . $view . '&id=' . $id;

		if ($catid > 1)
		{
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}
}
