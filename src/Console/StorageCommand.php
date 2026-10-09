<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiendos\MySitesIde\Clusters\Minikube\Traits\InteractsWithMinikube;

class StorageCommand extends Command
{
    use InteractsWithMinikube;

    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-storage')
            ->setDescription('The ability to mimic the storage class used by Digital Ocean')
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
        $storageChart = $this->chartPath('storage');
        $repoConfig = $this->deploymentFile($site, 'local.yaml');

        if (!file_exists($repoConfig)) {
            $output->writeln("<error>No $repoConfig - run deploy:site:bootstrap $site first</error>");
            return Command::FAILURE;
        }

        $output->writeln('<fg=green>About to mimic the digital ocean storage class</>');
        passthru("helm upgrade --install storage  --namespace $site --create-namespace  $storageChart -f $repoConfig");

        return Command::SUCCESS;
    }
}