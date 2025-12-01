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

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

abstract class RouteHelper
{
	/**
	 * Get the URL route for a events from a event ID, events category ID and language
	 *
	 * @param   integer  $id        The id of the events
	 * @param   integer  $catid     The id of the events's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the events
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getEventsRoute($id, $catid, $language = 0)
	{
		// Create the link
		$link = '?option=com_sdajem&view=events&id=' . $id;

		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a event from a event ID, events category ID and language
	 *
	 * @param   integer  $id        The id of the events
	 * @param   integer  $catid     The id of the events's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the events
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getEventRoute($id, $catid = 0, $language = 0)
	{
		// Create the link
		$link = '?option=com_sdajem&view=event&id=' . $id;

		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a event from a event ID, events category ID and language
	 *
	 * @param   integer  $id        The id of the location
	 * @param   integer  $catid     The id of the location category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the location
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getLocationRoute($id, $catid=0, $language = 0)
	{
		// Create the link
		$link = '?option=com_sdajem&view=location&id=' . $id;

		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a events category from a events category ID and language
	 *
	 * @param   mixed  $catid     The id of the events's category either an integer id or an instance of CategoryNode
	 * @param   mixed  $language  The id of the language being used.
	 *
	 * @return  string  The link to the events
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode) {
			$id = $catid->id;
		} else {
			$id = (int) $catid;
		}

		if ($id < 1) {
			$link = '';
		} else {
			// Create the link
			$link = '?option=com_sdajem&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled()) {
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}
