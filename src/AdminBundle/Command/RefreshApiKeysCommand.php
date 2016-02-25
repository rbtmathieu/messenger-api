<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshApiKeysCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('refresh:apikey')
            ->setDescription('Refresh ApiKeys')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'Select an User with its ID'
            );
    }

    protected function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $id = $inputInterface->getArgument('id');
        $refresh = $this->getContainer()->get('admin.refresh_apikeys');

        if ($id) {
            $refresh->refresh($id);
            $outputInterface->writeln('ApiKey for User #'.$id.' has been refreshed.');
        } else {
            $refresh->refreshAll();
            $outputInterface->writeln('ApiKeys has been refreshed');
        }
    }
}
