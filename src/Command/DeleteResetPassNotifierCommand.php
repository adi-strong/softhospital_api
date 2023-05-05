<?php

namespace App\Command;

use App\Entity\ResetPassNotifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'DeleteResetPassNotifierCommand',
    description: 'Add a short description for your command',
)]
class DeleteResetPassNotifierCommand extends Command
{
  protected static $defaultName = 'app:delete-reset-pass-notifier';

  public function __construct(private readonly EntityManagerInterface $em)
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->setDescription('Deletes ResetPassNotifier records older than 15 minutes.')
      ->setHelp('This command allows you to delete all ResetPassNotifier records that have been created more than 15 minutes ago.')
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $cutoff = new DateTime('-15 minutes');
    $passNotifiers = $this->em->getRepository(ResetPassNotifier::class)
      ->createQueryBuilder('rpn')
      ->where('rpn.releasedAt < :cutoff')
      ->setParameter('cutoff', $cutoff)
      ->getQuery()
      ->getResult();

    foreach ($passNotifiers as $notifier) $this->em->remove($notifier);
    $this->em->flush();

    $output->writeln(sprintf(
      'Deleted %d ResetPassNotifier records.',
      count($passNotifiers)
    ));

    return Command::SUCCESS;
  }
}
