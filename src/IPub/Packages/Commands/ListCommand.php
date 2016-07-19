<?php
/**
 * ListCommand.php
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
 * List packages command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ListCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure()
	{
		$this
			->setName('ipub:packages:list')
			->setDescription('List of installed packages.');
	}

	/**
	 * @param Input\InputInterface $input
	 * @param Output\OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		// Register available
		foreach ($this->packageManager->registerAvailable() as $item) {
			foreach ($item as $name => $action) {
				$output->writeln(sprintf('<info>%s : %s</info>', $action, $name));
			}
		}

		try {
			$packages = $this->repository->getPackages();

			$maxLength = 0;

			foreach ($packages as $package) {
				$length = strlen($package->getName());

				$maxLength = $maxLength > $length ? $maxLength : $length;
			}

			foreach ($packages as $package) {
				$output->writeln(sprintf(
					'<info>%'. $maxLength .'s</info> | status: <comment>%-12s</comment> | version: <comment>%s</comment>',
					$package->getName(),
					$this->packageManager->getStatus($package),
					$this->packageManager->getVersion($package)
				));
			}

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
