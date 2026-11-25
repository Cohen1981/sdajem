<?php

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File;
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
							$this->app->enqueueMessage(
								Text::_('COM_SDAJEM_CREATING_FOLDER')
								. $destinationFolder
							);
							Folder::create($destinationFolder);
						} catch (FilesystemException $e) {
							$this->app->enqueueMessage($e->getMessage(), 'error');
						}
					}

					try
					{
						$this->app->enqueueMessage(
							Text::_('COM_SDAJEM_FILES_COPY') . $sourceFolder
							. ' -> '
							. $destinationFolder
						);
						Folder::copy($sourceFolder, $destinationFolder,null,true,true);
					} catch (FilesystemException $e) {
						$this->app->enqueueMessage($e->getMessage(), 'error');
					}

					if ($type === 'update')
					{
						$this->deleteUnexistingFiles();
					}

					return true;
				}

				/**
				 * Delete files that should not exist
				 *
				 * @param bool  $dryRun          If set to true, will not actually delete files, but just report their status for use in CLI
				 * @param bool  $suppressOutput   Set to true to suppress echoing any errors, and just return the $status array
				 *
				 * @return  array
				 */
				public function deleteUnexistingFiles($dryRun = false, $suppressOutput = false)
				{
					$status = [
						'files_exist'     => [],
						'folders_exist'   => [],
						'files_deleted'   => [],
						'folders_deleted' => [],
						'files_errors'    => [],
						'folders_errors'  => [],
					];

					$files = [
						// From 6.2.0-beta2 to 6.2.0-beta3
						//'/media/vendor/tinymce/langs/cy.min.js',
						//'/media/vendor/tinymce/langs/cy.min.js.gz',
					];

					$folders = [
						// From 5.x to 6.0
						//'/libraries/vendor/voku/portable-utf8/src/voku/helper/data',
					];

					$status['files_checked']   = $files;
					$status['folders_checked'] = $folders;

					foreach ($files as $file) {
						if (is_file(JPATH_ROOT . $file)) {
							$status['files_exist'][] = $file;

							if ($dryRun === false) {
								if (File::delete(JPATH_ROOT . $file)) {
									$status['files_deleted'][] = $file;
								} else {
									$status['files_errors'][] = \sprintf('Error on deleting file or folder %s', $file);
								}
							}
						}
					}

					foreach ($folders as $folder) {
						if (is_dir(JPATH_ROOT . $folder)) {
							$status['folders_exist'][] = $folder;

							if ($dryRun === false) {
								if (Folder::delete(JPATH_ROOT . $folder)) {
									$status['folders_deleted'][] = $folder;
								} else {
									$status['folders_errors'][] = \sprintf('Error on deleting file or folder %s', $folder);
								}
							}
						}
					}

					$this->fixFilenameCasing();

					if ($suppressOutput === false && \count($status['folders_errors'])) {
						echo implode('<br>', $status['folders_errors']);
					}

					if ($suppressOutput === false && \count($status['files_errors'])) {
						echo implode('<br>', $status['files_errors']);
					}

					return $status;
				}

				/**
				 * Renames or removes incorrectly cased files.
				 *
				 * @return  void
				 *
				 * @since   3.9.25
				 */
				protected function fixFilenameCasing()
				{
					$files = [
						// From 5.x to 6.0
					];

					foreach ($files as $old => $expected) {
						$oldRealpath = realpath(JPATH_ROOT . $old);

						// On Unix without incorrectly cased file.
						if ($oldRealpath === false) {
							continue;
						}

						$oldBasename      = basename($oldRealpath);
						$newRealpath      = realpath(JPATH_ROOT . $expected);
						$newBasename      = basename($newRealpath);
						$expectedBasename = basename($expected);

						// On Windows or Unix with only the incorrectly cased file.
						if ($newBasename !== $expectedBasename) {
							// Rename the file.
							File::move(JPATH_ROOT . $old, JPATH_ROOT . $old . '.tmp');
							File::move(JPATH_ROOT . $old . '.tmp', JPATH_ROOT . $expected);

							continue;
						}

						// There might still be an incorrectly cased file on other OS than Windows.
						if ($oldBasename === basename($old)) {
							// Check if case-insensitive file system, eg on OSX.
							if (fileinode($oldRealpath) === fileinode($newRealpath)) {
								// Check deeper because even realpath or glob might not return the actual case.
								if (!\in_array($expectedBasename, scandir(\dirname($newRealpath)))) {
									// Rename the file.
									File::move(JPATH_ROOT . $old, JPATH_ROOT . $old . '.tmp');
									File::move(JPATH_ROOT . $old . '.tmp', JPATH_ROOT . $expected);
								}
							} else {
								// On Unix with both files: Delete the incorrectly cased file.
								if (is_file(JPATH_ROOT . $old)) {
									File::delete(JPATH_ROOT . $old);
								}
							}
						}
					}
				}
			}
		);
	}
};