<?php

namespace Bvisonl\LogBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOldLogsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bvisonl:logs:delete')
            ->setDescription('Delete the logs older than the days specified')
            ->addArgument('days', InputArgument::REQUIRED, 'The amount of days behind of logs to keep.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $days = $input->getArgument('days');

        $sixtyDays = new \DateTime();
        $sixtyDays->sub(new \DateInterval("P".$days."D"));

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('BvisonlLogBundle:Log','l');
        $qb->where('l.date <= :sixtyDays')->setParameter('sixtyDays', $sixtyDays);
        $rows = $qb->getQuery()->execute();
        $output->writeln("Finished deleting logs (".$rows.")");

    }
}