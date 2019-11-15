<?php
/**
 * EnableCommand.php
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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

use IPub\Packages\DependencyResolver;
use IPub\Packages\Exceptions;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Package enable command
 *
 * @package        iPublikuj:Packages!
 * @subpackage     Commands
 *
 * @author         Josef Kříž <pepakriz@gmail.com>
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class EnableCommand extends Command
{
	/**
	 * @return void
	 */
	protected function configure() : void
	{
		$this
			->setName('ipub:packages:enable')
			->addArgument('package', Input\InputArgument::REQUIRED, 'Package name')
			->addOption('noconfirm', NULL, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Enable package.');
	}

	/**
	 * @param Input\InputInterface $input
	 * @param Output\OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output) : void
	{
		// Register available
		foreach ($this->packageManager->registerAvailable() as $item) {
			foreach ($item as $name => $action) {
				$output->writeln(sprintf('<info>%s : %s</info>', $action, $name));
			}
		}

		$package = $this->repository->findPackage($input->getArgument('package'));

		try {
			$problem = $this->packageManager->testEnable($package);

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

			return;
		}

		if (!$input->getOption('noconfirm') && count($problem->getSolutions()) > 0) {
			$output->writeln(sprintf('<info>enabling : %s</info>', $package->getName()));

			foreach ($problem->getSolutions() as $job) {
				$output->writeln(sprintf('<info>%s : %s</info>', $job->getAction(), $job->getPackage()->getName()));
			}

			/** @var QuestionHelper $dialog */
			$dialog = $this->getHelperSet()->get('question');

			$question = new ConfirmationQuestion('Continue with this action? [y/N]', FALSE);

			if (!$dialog->ask($input, $output, $question)) {
				return;
			}
		}

		try {
			foreach ($problem->getSolutions() as $job) {
				if ($job->getAction() === DependencyResolver\Job::ACTION_ENABLE) {
					$this->packageManager->enable($job->getPackage());
					$output->writeln(sprintf('Package \'%s\' has been enabled.', $job->getPackage()->getName()));

				} elseif ($job->getAction() === DependencyResolver\Job::ACTION_DISABLE) {
					$this->packageManager->disable($job->getPackage());
					$output->writeln(sprintf('Package \'%s\' has been disabled.', $job->getPackage()->getName()));
				}
			}

			$this->packageManager->enable($package);

			$output->writeln(sprintf('Package \'%s\' has been enabled.', $input->getArgument('package')));

		} catch (Exceptions\InvalidArgumentException $e) {
			$output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
		}
	}
}
