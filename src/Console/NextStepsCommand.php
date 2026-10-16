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
            "<fg=green>Congratulations you have just launched Minikube locally and configured it for: $site</>",
            '',
            '<fg=green>Your next steps can be:</>',
        ]);

        //building images is deploy's job - only point at it when it's installed
        //building images and deploying is a deploy plugin's job - only point at it when it's installed
        if ($this->getApplication()?->has('deploy:k8s-build-push')) {
            $output->writeLn("my-sites-ide deploy:k8s-build-push $site local");
        } else {
            $output->writeLn("build and tag your site's images (e.g. with yiendos/my-sites-ide-deploy-k8s)");
            $output->writeLn('my-sites-ide clusters:minikube-load-images ' . $site);
        }

        return Command::SUCCESS;
    }
}