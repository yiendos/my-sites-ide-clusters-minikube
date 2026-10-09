<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckSystemRequirementsCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-system-requirements')
            ->setDescription('In order to run minikube you need some stuff installed on your system')
        ;
    }
    /**
     * Invoke vs execute because you cannot Dependancy Inject the requirements because of the command concrete cast
     * Same same, same but different ensure that we execute the user input
     *
     * @param OutputInterface $output
     * @param Application $application
     * @param InputInterface $input
     * @param SymfonyStyle $io
     * @return integer
     */
    public function __invoke(OutputInterface $output, Application $application, InputInterface $input, SymfonyStyle $io): int
    {
        //some sort of virtualisation require 
        if (!$io->confirm('Please confirm that you have some sort of virtualisation installed: [ Docker, QEMU, Hyperkit, Hyper-V, KVM, Parallels, Podman, VirtualBox, or VMware Fusion/Workstation ]')) {
            $io->error('You need to install some sort of virtualisation first, before you can run minikube');
        }

        $output->writeLn([
            '<info>Checking for minikube</>', 
            '',
        ]);

        if (strlen(exec('command -v minikube')) == false) 
        {
            $io->error("You don't have Minikube installed"); 
            $io->writeln('<href=https://minikube.sigs.k8s.io/docs/start>Install minikube</>');
            return Command::FAILURE;
        }
       
        $output->writeLn([
            'Pete Tong - We continue...', 
            '',
            '<info>Checking for kubectl</>',
            ''
        ]);

        if (strlen(exec('command -v kubectl')) == false) 
        {
            $io->error("You don't have Minikube installed"); 
            $io->writeln('<href=https://kubernetes.io/docs/tasks/tools/>Install kubectl</>');
            return Command::FAILURE;
        }

        $output->writeLn([
            'We continue...',
            '',
            '<info>Checking for helm</>', 
            ''
        ]);

        //we also need to check for helm 
        if (strlen(exec('command -v helm')) == false) 
        {
            $io->error("You don't have Helm installed"); 
            $io->writeln('<href=https://helm.sh/docs/intro/install/>Install Helm</>');
            return Command::FAILURE;
        }

        $output->writeLn([
            'We continue...', 
            '',
            '<info>Checking for cert manager </>',
            ''
        ]);

        //and that we have the cert-manager available locally 
        if (strlen(exec('helm repo list | grep "jetstack" ')) == false) 
        {
            $io->error("You don't have the Helm Jetstack repo installed"); 
            $io->writeln('helm install oci://quay.io/jetstack/charts/cert-manager:v1.19.0');
            return Command::FAILURE;
        }

        $output->writeln([
            '', 
            'We continue...'
        ]);

        $io->info("Congratulations your system checks out, get ready for the launch");
        
        if ($io->confirm('Would you like to start and configure Minikube now')) 
        {
            $site = $io->ask('Which site would you like to configure Minikube for? [./Repos/NAME/deploy]'); 

            $startConfigureInput = new ArrayInput(['command' => 'clusters:minikube-start-configure', 'site' => $site]);

            $application->doRun($startConfigureInput, $output);
        }

        return Command::SUCCESS;
    }
}