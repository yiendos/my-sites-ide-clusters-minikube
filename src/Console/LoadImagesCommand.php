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
        $registry = getenv('MINIKUBE_REGISTRY') ?: 'registry.digitalocean.com/yiendos';
        $images = ['fpm-server', 'web-server', 'cron', 'cli'];

        $io = (new SymfonyStyle($input, $output));
        $io->progressStart(count($images));

        $output->writeln([
            "",
            "<fg=green>About load images into minikube for site: $site</>",
            ""
        ]);

        foreach ($images as $image)
        {
            $command = "minikube image load --overwrite=true $registry/$site:$image-latest";

            $output->writeLn(["", $command, ""]);

            passthru($command);

            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }
}