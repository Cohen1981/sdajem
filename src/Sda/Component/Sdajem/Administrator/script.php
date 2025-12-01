<?php

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\Folder;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements ServiceProviderInterface {
	public function register(Container $container)
	{
		$container->set(
			InstallerScriptInterface::class,
			new class (
				$container->get(AdministratorApplication::class),
				$container->get(DatabaseInterface::class)
			) implements InstallerScriptInterface {
				private AdministratorApplication $app;
				private DatabaseInterface $db;

				/**
				 * Minimum Joomla version to check
				 *
				 * @var    string
				 * @since  1.0.0
				 */
				private $minimumJoomlaVersion = '5.4';
				/**
				 * Minimum PHP version to check
				 *
				 * @var    string
				 * @since  1.0.0
				 */
				private $minimumPHPVersion = '8.3';

				public function __construct(AdministratorApplication $app, DatabaseInterface $db)
				{
					$this->app = $app;
					$this->db  = $db;
				}

				public function install(InstallerAdapter $adapter): bool
				{
					$this->app->enqueueMessage(Text::_('COM_SDAJEM_INSTALLERSCRIPT_INSTALL'));

					return true;
				}

				public function update(InstallerAdapter $adapter): bool
				{
					$this->app->enqueueMessage(Text::_('COM_SDAJEM_INSTALLERSCRIPT_UPDATE'));

					return true;
				}

				public function uninstall(InstallerAdapter $adapter): bool
				{
					$this->app->enqueueMessage(Text::_('COM_SDAJEM_INSTALLERSCRIPT_UNINSTALL'));

					return true;
				}

				public function preflight(string $type, InstallerAdapter $adapter): bool
				{
					if ($type !== 'uninstall') {
						// Check for the minimum PHP version before continuing
						if (!empty($this->minimumPHPVersion) && version_compare(PHP_VERSION, $this->minimumPHPVersion, '<')) {
							Log::add(
								Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPHPVersion),
								Log::WARNING,
								'jerror'
							);
							return false;
						}
						// Check for the minimum Joomla version before continuing
						if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<')) {
							Log::add(
								Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
								Log::WARNING,
								'jerror'
							);
							return false;
						}
					}
					echo Text::_('COM_SDAJEM_INSTALLERSCRIPT_PREFLIGHT');

					return true;
				}

				public function postflight(string $type, InstallerAdapter $adapter): bool
				{
					$this->app->enqueueMessage(Text::_('COM_SDAJEM_INSTALLERSCRIPT_POSTFLIGHT'));

					$destinationFolder = JPATH_SITE . '/' . "images/lagerplanung";
					$sourceFolder = JPATH_SITE . '/media/com_sdajem/images/lagerplanung';

					if(is_dir($sourceFolder) && !is_dir($destinationFolder))
					{
						try
						{
							Folder::create($destinationFolder);
						} catch (FilesystemException $e) {
							$this->app->enqueueMessage($e->getMessage(), 'error');
						}
					}

					try
					{
						Folder::copy($sourceFolder, $destinationFolder,null,true,true);
					} catch (FilesystemException $e) {
						$this->app->enqueueMessage($e->getMessage(), 'error');
					}

					$this->deleteUnexistingFiles();

					return true;
				}

				private function deleteUnexistingFiles()
				{
					$files = [];  // overwrite this line with your files to delete

					if (empty($files)) {
						return;
					}

					foreach ($files as $file) {
						try {
							File::delete(JPATH_ROOT . $file);
						} catch (FilesystemException $e) {
							echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br>';
						}
					}
				}
			}
		);
	}
};