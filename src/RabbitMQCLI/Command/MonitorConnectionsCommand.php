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

class MonitorConnections extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'monitor_connections';

    protected function configure()
    {
        $this
            ->setDescription("Loops and displays incoming and outgoing connections")
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

        $connections_queue = [];

        while (true) {
            $connections = $conn->monitorConnections();

            foreach ($connections as $connection) {
                $server_ip = $connection->ip;
                $status = $connection->status;
                $time = date("Y-m-d H:i:s");

                switch ($status) {
                    case "Connected":
                        $status = CliText::color($status, "green");
                        break;

                    case "Disconnected":
                        $status = CliText::color($status, "red");
                        break;
                }

                $output->writeln("[$time UTC] $server_ip -> $status");
                usleep(25000);
            }

            sleep(5);
        }

        return Command::SUCCESS;
    }
}
