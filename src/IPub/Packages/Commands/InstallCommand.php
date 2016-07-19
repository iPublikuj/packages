<?php
/**
 * InstallCommand.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
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

use IPub;
use IPub\Packages;
use IPub\Packages\DependencyResolver;
use IPub\Packages\Exceptions;

/**
 * Package install command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class InstallCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure()
	{
		$this
			->setName('ipub:packages:install')
			->addArgument('package', Input\InputArgument::REQUIRED, 'Package name')
			->setDescription('Install package.');
	}

	/**
	 * @param Input\InputInterface $input
	 * @param Output\OutputInterface $output
	 * 
	 * @return void
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		try {
			$this->packageManager->install($input->getArgument('package'));

			$output->writeln(sprintf('Package \'%s\' has been installed.', $input->getArgument('package')));

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
