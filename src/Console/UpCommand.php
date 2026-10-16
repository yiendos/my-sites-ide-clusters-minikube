<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiendos\MySitesIde\Clusters\Minikube\Traits\InteractsWithMinikube;

class UpCommand extends Command
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
            ->setName('clusters:minikube-up')
            ->setDescription('Sping up minikube locally, to enable test deployments')
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
        $createStoragePath = $this->deploymentFile($site, 'create-storage.sh');

        if (!file_exists($createStoragePath)) {
            $output->writeln("<error>No $createStoragePath - run deploy:k8s-bootstrap $site first</error>");
            return Command::FAILURE;
        }

        $output->writeln('<fg=green>About to start the minikube instance</>');

        $addons = getenv('MINIKUBE_ADDONS') ?: 'dashboard,metrics-server,traefik';
        $runtime = getenv('MINIKUBE_CONTAINER_RUNTIME') ?: 'containerd';

        passthru("minikube start --addons=$addons --container-runtime=$runtime && minikube cp $createStoragePath /tmp/create-storage.sh && minikube ssh 'sh /tmp/create-storage.sh'");

        return Command::SUCCESS;
    }
}