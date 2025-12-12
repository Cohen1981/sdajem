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
	public ?int $id;

	public ?string $alias;

	public ?string $slug;

	public ?int $published = 1;

	public ?string $publish_up = '';

	public ?string $publish_down = '';
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
				$defaultValue = $property->getDefaultValue();
				$name        = $property->getName();
				$this->$name  = $defaultValue;
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
			// If the property exists
			if ($selfReflection->hasProperty($key))
			{
				// Get the default value
				$defaultValue = $selfReflection->getProperty($key)->getDefaultValue();
				$types = [];

				// Get the type of the property
				if ($selfReflection->getProperty($key)->hasType())
				{
					$type = $selfReflection->getProperty($key)->getType();

					// If the type is a named type
					if ($type instanceof \ReflectionNamedType)
					{
						$types[] = $type->getName();
					}
					// If the type is a union type
					elseif ($type instanceof \ReflectionUnionType)
					{
						$rTypes = $type->getTypes();

						// Get the types of the union
						foreach ($rTypes as $rType)
						{
							$types[] = $rType->getName();
						}
					}
				}

				if (isset($value))
				{
					// Convert empty strings to null if the property is an int
					if (in_array('int', $types) and $value === '')
					{
						$value = null;
					}

					// Convert arrays to json if the property is not an array
					if (!in_array('array', $types) && in_array('string', $types) && is_array($value))
					{
						$value = json_encode($value);
					}
				}

				$item->$key   = (!isset($value)) ? $defaultValue : $value;
			}
		}

		if (!empty($item->alias) && !empty($item->id))
		{
			$item->slug = $item->id . ':' . $item->alias;
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
