<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeUserStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('user:status')
            ->setDescription('Change User status')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Select an User with its ID'
            );
    }

    protected function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $id = $inputInterface->getArgument('id');
        $status = $this->getContainer()->get('admin.change_user_status');

        $status->changeStatus($id);
        $outputInterface->writeln('Status of User #'.$id.' has been changed.');
    }
}
