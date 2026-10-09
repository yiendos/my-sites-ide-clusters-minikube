<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadImagesCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('clusters:minikube-load-images')
            ->setDescription('The ability to load docker images into minikube')
            ->addArgument('site', InputArgument::REQUIRED, 'Which Repository site do you want to load images for? [./Repos/NAME/deploy]')
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

        $io = (new SymfonyStyle($input, $output)); 
        $io->progressStart(100);

        $output->writeln([
            "",
            "<fg=green>About load images into minikube for site: $site</>",
            ""
        ]);

        $output->writeLn([
            "",
            "minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:fpm-server-latest",
            ""
        ]);

        passthru("minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:fpm-server-latest");

        $io->progressAdvance(20);

        $output->writeLn([
            "",
            "",
            "minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:web-server-latest",
            ""
        ]);

        passthru("minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:web-server-latest");

        $io->progressAdvance(20);

        $output->writeLn([
            "",
            "",
            "minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:cron-latest",
            ""
        ]);

        passthru("minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:cron-latest");

        $io->progressAdvance(20);

        $output->writeLn([
            "",
            "",
            "minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:cli-latest",
            "",
        ]);

        passthru("minikube image load --overwrite=true registry.digitalocean.com/yiendos/$site:cli-latest");

        $io->progressFinish();

        return Command::SUCCESS;
    }
}