<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NextStepsCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-next-steps')
            ->setDescription('The ability to provide feedback, once the minikube instance is up and running')
            ->addArgument('site', InputArgument::REQUIRED, 'Which Repository site do you want to deploy? [./Repos/NAME/deploy]')
        ;
    }
    /**
     * After the command has been configured, the user has provided input then run the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $site = $input->getArgument('site');

        $output->writeln([
            '',
            '<fg=green>Congratulations you have just launched Minikube locally and configured it for: $site</>',
            '',
            '<fg=green>Your next steps can be:</>',
        ]);

        $output->writeLn('my-sites-ide deploy:site:build ' . $site);
        $output->writeLn("my-sites-ide minikube:load-images");

        return Command::SUCCESS;
    }
}