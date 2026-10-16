<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PortForwardCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-port-forward')
            ->setDescription('The ability to access your site in the browser through localhost')
            ->addArgument('site', InputArgument::REQUIRED, 'Which Repository site do you want to access? [./Repos/NAME/deploy]')
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

        $port = getenv('MINIKUBE_PORT_FORWARD') ?: '8090';
        $command = "kubectl --namespace $site port-forward service/$site-webserver $port:80";

        $output->writeln([$command, "<fg=green>Serving $site on http://localhost:$port</>"]);

        passthru($command);

        return Command::SUCCESS;
    }
}