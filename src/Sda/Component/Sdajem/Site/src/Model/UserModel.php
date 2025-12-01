<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

/**
 * @since       1.0.0
 */
class UserModel
{
	/**
	 * @var array|null
	 * The Joomla user custom fields data
	 * @since 1.0.0
	 */
	public ?array $userData = [];

	/**
	 * @var array|null
	 * The user profile information if exists
	 * @since 1.0.0
	 */
	public ?array $profile = [];

	/**
	 * @var mixed|null
	 * The user data or object
	 * @since 1.0.0
	 */
	public ?User $user = null;

	/**
	 * Constructor for initializing the user and their associated data, including profile and custom fields.
	 *
	 * @param   int|null  $userId  The ID of the user to load. If null, no user data will be loaded.
	 *
	 * @since 1.0.0
	 */
	public function __construct(int $userId = null)
	{
		if (isset($userId))
		{
			$this->user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userId);

			if (!empty(UserHelper::getProfile($userId)->profile))
			{
				$this->profile = UserHelper::getProfile($userId)->profile;
			}

			$userdata = FieldsHelper::getFields('com_users.user', $this->user, true);
			$tmp          = $userdata ?? array();
			$customFields = array();

			foreach ($tmp as $customField)
			{
				$customFields[$customField->name] = $customField;
			}

			$this->userData = $customFields;
		}
	}
}
