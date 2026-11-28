<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Sda\Component\Sdajem\Site\Model\UserModel;

/**
 * @package     Sda\Component\Sdajem\Site\Model\Item
 * For programming convenience, the class gives type hinting for the class properties.
 * @since       1.4.0
 */
class Comment extends CommentTableItem
{
	/**
	 * The user who wrote the comment.
	 * @var UserModel|null
	 * @since 1.5.3
	 */
	public ?UserModel $commentUser;

	/**
	 * Creates an instance of the class using the provided array data.
	 *
	 * @param   array  $data  The array containing data to populate the class properties.
	 *
	 * @return static Returns an instance of the class populated with the provided data.
	 * @since 1.5.3
	 */
	public static function createFromArray(array $data = []): static
	{
		$item = parent::createFromArray($data);

		if (isset($item->users_user_id))
		{
			$item->commentUser = new UserModel($item->users_user_id);
		}

		return $item;
	}
}
