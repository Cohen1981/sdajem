<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use ReflectionObject;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemInterface;
use stdClass;

/**
 * @package Sda\Component\Sdajem\Administrator\Trait
 * @author  Alexander Bahlo <abahlo@hotmail.de>
 * @since   1.4.0
 * For programming convenience only
 */
class ItemClass extends stdClass implements ItemInterface
{
	/**
	 * @param   array|stdClass|null  $data  The data to convert to an object
	 *
	 * @since 1.5.3
	 */
	public function __construct(array|stdClass $data = null)
	{
		if (!$data)
		{
			$selfReflection = new ReflectionObject($this);

			foreach ($selfReflection->getProperties() as $property)
			{
				$name        = $property->getName();
				$this->$name = null;
			}
		}
		elseif ($data instanceof stdClass)
		{
			$this->createFromObject($data);
		}
		else
		{
			$this->createFromArray($data);
		}
	}

	/**
	 * @param   array  $data  the data array to convert
	 *
	 * @return static
	 * @since 1.5.3
	 *
	 */
	public static function createFromArray(array $data = []): static
	{
		$item           = new static;
		$selfReflection = new ReflectionObject($item);

		foreach ($data as $key => $value)
		{
			if ($selfReflection->hasProperty($key))
			{
				$item->$key = $value;
			}
		}

		return $item;
	}

	/**
	 * @param   stdClass  $data  The class to convert
	 *
	 * @return static
	 * @since 1.5.3
	 *
	 */
	public static function createFromObject(stdClass $data = new stdClass): static
	{
		return static::createFromArray((array) $data);
	}

	/**
	 * @since 1.5.3
	 * @return array
	 */
	public function toArray(): array
	{
		return (array) $this;
	}
}
