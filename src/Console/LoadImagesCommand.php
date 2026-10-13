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
            ->addArgument('site', InputArgument::REQUIRED, 'Which Repository site do you want to load images for? [./Repos/NAME]')
            ->addArgument('images', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, "Full image references to load, e.g. from deploy:k8s-build-push - without them, <MINIKUBE_REGISTRY>/<site>:{fpm-server,web-server,cron,cli}-latest")
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
        $images = $input->getArgument('images');

        $io = (new SymfonyStyle($input, $output));

        //without references (a deploy plugin passes the exact images it built), fall back to
        //the <registry>/<site>:<image>-latest tags
        if (!$images) {
            $registry = getenv('MINIKUBE_REGISTRY');

            if (!$registry) {
                $io->error('Set MINIKUBE_REGISTRY in the root .env to the registry your images are tagged with, e.g. registry.digitalocean.com/<your-registry> - or pass the images to load');

                return Command::FAILURE;
            }

            $images = array_map(fn (string $image): string => "$registry/$site:$image-latest", ['fpm-server', 'web-server', 'cron', 'cli']);
        }

        $io->progressStart(count($images));

        $output->writeln([
            "",
            "<fg=green>About load images into minikube for site: $site</>",
            ""
        ]);

        foreach ($images as $image)
        {
            $command = 'minikube image load --overwrite=true ' . escapeshellarg($image);

            $output->writeLn(["", $command, ""]);

            passthru($command, $exitCode);

            if ($exitCode !== 0) {
                $io->progressFinish();
                $io->error("Couldn't load $image into minikube");

                return Command::FAILURE;
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }
}