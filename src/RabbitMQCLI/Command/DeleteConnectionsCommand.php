<?php

namespace App\Command;

require_once __DIR__ . "/../Classes/Connections.php";

use Classes\Connections;
use PHP_Tui\CliTable;
use PHP_Tui\CliText;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DeleteConnections extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'delete_connections';

    protected function configure()
    {
        $this
            ->setDescription("Deletes all connections on specified queue")
            ->setHelp("Ensure that you have added your username and password
                \r  to the .env file in the main repo
                \n\r  View the README.md for more help")
            ->addArgument(
                'Queue Name',
                InputArgument::REQUIRED,
                "Name of queue to delete connections on"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }
}
