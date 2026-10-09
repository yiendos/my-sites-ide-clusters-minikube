<?php 

namespace Yiendos\Deploy\Console\Minikube; 

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class StartConfigureMinikubeCommand extends Command
{
    /**
     * The ability to configure the console command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('minikube:start-configure')
            ->setDescription('A one line command to start minikube, install a mimic storage class, create phsyical folders in the minikube instance for persistence volumes')
            ->addArgument('site', InputArgument::REQUIRED, 'Which Repository site do you want to deploy? [./Repos/NAME/deploy]')
        ;
    }
    /**
     * Invoke vs execute because you cannot Dependancy Inject the requirements because of the command concrete cast
     * Same same, same but different ensure that we execute the user input
     *
     * @param OutputInterface $output
     * @param Application $application
     * @param InputInterface $input
     * @return integer
     */
    public function __invoke(OutputInterface $output, Application $application, InputInterface $input): int
    {
        $site = $input->getArgument('site');

        $upInput = new ArrayInput(['command' => 'minikube:up', 'site' => $site]);

        $application->doRun($upInput, $output);

        $storageInput = new ArrayInput(['command' => 'minikube:storage', 'site' => $site]); 

        $application->doRun($storageInput,  $output);

        $certificateInput = new ArrayInput(['command' => 'minikube:cert-manager']); 

        $application->doRun($certificateInput, $output);

        $nextStepsInput = new ArrayInput(['command' => 'minikube:next-steps', 'site' => $site]); 

        $application->doRun($nextStepsInput, $output);

        return Command::SUCCESS;
    }
}