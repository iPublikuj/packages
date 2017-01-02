<?php
/**
 * DisableCommand.php
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
 * Disable package command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class DisableCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure()
	{
		$this
			->setName('ipub:packages:disable')
			->addArgument('package', Input\InputArgument::REQUIRED, 'Package name')
			->addOption('noconfirm', NULL, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Disable package.');
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

		$package = $this->repository->findPackage($input->getArgument('package'));

		try {
			$problem = $this->packageManager->testDisable($package);

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

			return;
		}

		if (!$input->getOption('noconfirm') && count($problem->getSolutions()) > 0) {
			foreach ($problem->getSolutions() as $job) {
				$output->writeln(sprintf('<info>%s : %s</info>', $job->getAction(), $job->getPackage()->getName()));
			}

			$output->writeln(sprintf('<info>disabling : %s</info>', $package->getName()));

			$dialog = $this->getHelperSet()->get('dialog');
			if (!$dialog->askConfirmation($output, '<question>Continue with this actions? [y/N]</question> ', FALSE)) {
				return;
			}
		}

		try {
			foreach ($problem->getSolutions() as $job) {
				if ($job->getAction() === DependencyResolver\Job::ACTION_ENABLE) {
					$this->packageManager->disable($job->getPackage());
					$output->writeln(sprintf('Package \'%s\' has been enabled.', $job->getPackage()->getName()));

				} elseif ($job->getAction() === DependencyResolver\Job::ACTION_DISABLE) {
					$this->packageManager->enable($job->getPackage());
					$output->writeln(sprintf('Package \'%s\' has been disabled.', $job->getPackage()->getName()));

				}
			}

			$this->packageManager->disable($package);
			$output->writeln(sprintf('Package \'%s\' has been disabled.', $input->getArgument('package')));

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
