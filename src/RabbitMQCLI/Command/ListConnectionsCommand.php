<?php

namespace App\Command;

require_once __DIR__ . "/../Classes/Connections.php";

use Classes\Connections;
use PHP_Tui\CliTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ListConnections extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'list_connections';

    protected function configure()
    {
        $this
            ->setDescription("Lists active connections to a queue")
            ->setHelp("Ensure that you have added your username and password
                \r  to the .env file in the main repo
                \n\r  View the README.md for more help")
            ->addArgument(
                'Queue Name',
                InputArgument::REQUIRED,
                "Name of queue to view connections for"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue_name = $input->getArgument('Queue Name');
        $conn = new Connections($queue_name);
        $server_ips = $conn->getConnections();

        $table = new CliTable();
        $table->setHeader(["Server IP"]);

        foreach ($server_ips as $server_ip) {
            $row = [
                "Server IP" => $server_ip
            ];
            $table->addRow($row);
        }

        $table->draw();

        return Command::SUCCESS;
    }
}
