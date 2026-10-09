<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCertificateManagerCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-cert-manager')
            ->setDescription('The ability to install certificate manager for k8s')
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
        $output->writeln('<fg=green>About to k8s certificate manager</>');

        passthru("helm upgrade --install cert-manager jetstack/cert-manager --namespace cert-manager --create-namespace --version v1.15.2 --set crds.enabled=true");

        return Command::SUCCESS;
    }
}