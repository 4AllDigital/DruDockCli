<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command\Redis;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Docker\Drupal\Style\DockerDrupalStyle;

/**
 * Class WatchCommand
 * @package Docker\Drupal\Command\redis
 */
class RedisPingCommand extends Command
{
  protected function configure()
  {
      $this
          ->setName('redis:ping')
          ->setDescription('Ping Redis')
          ->setHelp("This command will ping REDIS and respond with PONG if all is running well.")
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $application = $this->getApplication();
    $io = new DockerDrupalStyle($input, $output);

    $io->section("REDIS ::: Ping");

    if($config = $application->getAppConfig($io)) {
      $appname = $config['appname'];
    }

    if($application->checkForAppContainers($appname, $io)){
      $command = $application->getComposePath($appname).'exec -T redis redis-cli ping';
    }

    $process = new Process($command);
    $process->setTimeout(2);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
    $out = $process->getOutput();
    $io->info($out);
  }
}