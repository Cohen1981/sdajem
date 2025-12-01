<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sda\Component\Sdajem\Administrator\Service\HTML\Icon;

/**
 * The SdajemComponent class represents a custom component that extends the MVCComponent.
 * It implements BootableExtensionInterface and CategoryServiceInterface to provide
 * functionality for bootstrapping and category-related operations.
 * This class utilizes the CategoryServiceTrait and HTMLRegistryAwareTrait traits
 * to enhance its capabilities for category handling and HTML service registration.
 *
 * @since 1.0.0
 */
class SdajemComponent extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface
{
	use CategoryServiceTrait;
	use HTMLRegistryAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @since   1.0.0
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 * @throws NotFoundExceptionInterface
	 * @throws ContainerExceptionInterface
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('sdajemIcon', new Icon($container->get(SiteApplication::class)));
	}

	/**
	 * @since 1.0.0
	 *
	 * @param   string|null  $section  The section
	 *
	 * @return string
	 */
	protected function getStateColumnForSection(string $section = null): string
	{
		return 'published';
	}
}
