<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Sda\Component\Sdajem\Administrator\Extension\SdajemComponent;

return new class implements ServiceProviderInterface {

	/**
	 * Registers services and configurations within the provided container.
	 *
	 * @param   Container  $container  The service container used for registering and resolving dependencies.
	 *
	 * @return void
	 * @since  joomla 3.1
	 */
	public function register(Container $container) {
		$container->registerServiceProvider(new CategoryFactory('\\Sda\\Component\\Sdajem'));
		$container->registerServiceProvider(new MVCFactory('\\Sda\\Component\\Sdajem'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Sda\\Component\\Sdajem'));
		//$container->registerServiceProvider(new RouterFactory('\\Sda\\Component\\Sdajem'));
		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new SdajemComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				//$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
