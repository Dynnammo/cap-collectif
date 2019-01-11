<?php

namespace Capco\AppBundle\Command;

use Capco\AppBundle\Toggle\Manager;
use FOS\UserBundle\Doctrine\UserListener;
use Gedmo\Timestampable\TimestampableListener;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sonata\EasyExtendsBundle\Mapper\DoctrineORMMapper;
use Sonata\MediaBundle\Listener\ORM\MediaEventSubscriber;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class LoadBenchmarkDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('capco:load-benchmark-data')
            ->setDescription('A bunch of fixtures to benchmark the application')
            ->addOption(
                'force',
                false,
                InputOption::VALUE_NONE,
                'set the --force option to run this command'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('force')) {
            $output->writeln(
                'This command will clear your database and populate it with lots of data, if you\'re sure that you want those data, go ahead and add --force'
            );
            $output->writeln('Please set the --force option to run this command');

            return;
        }

        $this->disableListeners($output, [
            TimestampableListener::class,
            MediaEventSubscriber::class,
            DoctrineORMMapper::class,
            UserListener::class,
        ]);

        $this->loadFixtures($output);

        $output->writeln('<info>Database loaded !</info>');

        $this->loadToggles($output);

        $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->clear();

        $this->populateElasticsearch($output);

        $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->clear();

        $this->recalculateCounters($output);

        $output->writeln('Load benchmark data completed');
    }

    protected function loadFixtures(OutputInterface $output)
    {
        $command = $this->getApplication()->find('hautelook_alice:doctrine:fixtures:load');
        $input = new ArrayInput([
            'command' => 'hautelook_alice:doctrine:fixtures:load',
            '--fixtures' => 'src/Capco/AppBundle/DataFixtures/ORM/Benchmark',
        ]);
        $input->setInteractive(false);
        $command->run($input, $output);
    }

    protected function loadToggles(OutputInterface $output)
    {
        $command = $this->getApplication()->find('capco:reset-feature-flags');
        $input = new ArrayInput([
            '--force' => true,
        ]);
        $input->setInteractive(false);
        $command->run($input, $output);

        $toggleManager = $this->getContainer()->get(Manager::class);
        $toggleManager->deactivate('shield_mode');
    }

    protected function populateElasticsearch(OutputInterface $output)
    {
        $this->runCommands(
            [
                'capco:es:create' => ['--quiet' => true, '--no-debug' => true],
            ],
            $output
        );

        $this->runCommands(
            [
                'capco:es:populate' => ['--quiet' => true, '--no-debug' => true],
            ],
            $output
        );
    }

    protected function recalculateCounters(OutputInterface $output)
    {
        $this->runCommands(
            [
                'capco:compute:users-counters' => ['--force' => true],
                'capco:compute:counters' => ['--force' => true],
                'capco:compute:rankings' => [],
            ],
            $output
        );
    }

    private function runCommands(array $commands, $output)
    {
        foreach ($commands as $key => $value) {
            $input = new ArrayInput($value);
            $input->setInteractive(false);
            $this->getApplication()
                ->find($key)
                ->run($input, $output);
        }
    }

    private function disableListeners(OutputInterface $output, array $whitelist): void
    {
        $eventManager = $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getEventManager();

        foreach ($eventManager->getListeners() as $event => $listeners) {
            foreach ($listeners as $key => $listener) {
                if (\is_string($listener) || \in_array(\get_class($listener), $whitelist, true)) {
                    continue;
                }
                if (method_exists($listener, 'getSubscribedEvents')) {
                    $eventManager->removeEventListener($listener->getSubscribedEvents(), $listener);
                    $output->writeln('Disabled <info>' . \get_class($listener) . '</info>');
                }
            }
        }
    }
}
