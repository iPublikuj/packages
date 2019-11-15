<?php
/**
 * UninstallCommand.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 * @since          2.0.0
 *
 * @date           19.07.16
 */

declare(strict_types = 1);

namespace IPub\Packages\Commands;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

use IPub\Packages\Exceptions;

/**
 * Package install command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class UninstallCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure() : void
	{
		$this
			->setName('ipub:packages:uninstall')
			->addArgument('package', Input\InputArgument::REQUIRED, 'Package name')
			->setDescription('Uninstall package.');
	}

	/**
	 * @param Input\InputInterface $input
	 * @param Output\OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output) : void
	{
		try {
			$this->packageManager->uninstall($input->getArgument('package'));

			$output->writeln(sprintf('Package \'%s\' has been installed.', $input->getArgument('package')));

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
