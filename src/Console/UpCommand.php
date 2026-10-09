<?php 

namespace Yiendos\Deploy\Console\Minikube; 

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('minikube:up')
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
        $createStoragePath = CONSOLE_ROOT . "/Repos/$site/_build/deployment/create-storage.sh";

        $output->writeln('<fg=green>About to start the minikube instance</>');

        passthru("minikube start --addons=dashboard,metrics-server,traefik --container-runtime=containerd && minikube cp $createStoragePath /tmp/create-storage.sh && minikube ssh 'sh /tmp/create-storage.sh'");

        return Command::SUCCESS;
    }
}