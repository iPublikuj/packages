<?php
/**
 * SyncCommand.php
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
 * Synchronize packages command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class SyncCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure() : void
	{
		$this
			->setName('ipub:packages:sync')
			->addOption('composer', NULL, Input\InputOption::VALUE_NONE, 'run as composer script')
			->setDescription('Synchronize packages.');
	}

	/**
	 * @param Input\InputInterface $input
	 * @param Output\OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output) : void
	{
		if ($input->getOption('composer') === FALSE) {
			$output->writeln('+---------------------------------+');
			$output->writeln('| Package manager synchronization |');
			$output->writeln('+---------------------------------+');
		}

		// Register available
		foreach ($this->packageManager->registerAvailable() as $item) {
			foreach ($item as $name => $action) {
				$output->writeln(sprintf('<info>%s : %s</info>', $action, $name));
			}
		}

		try {
			foreach ($this->packageManager->enableAvailable() as $item) {
				foreach ($item as $name => $action) {
					$output->writeln(sprintf('<info>%s : %s</info>', $action, $name));
				}
			}
/*
			foreach ($this->packageManager->disableAbsent() as $item) {
				foreach ($item as $name => $action) {
					$output->writeln(sprintf('<info>%s : %s</info>', $action, $name));
				}
			}
*/
		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
