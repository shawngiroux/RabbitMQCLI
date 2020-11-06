<?php

namespace Classes;

require_once __DIR__ . "/../Structs/MonitorConnection.php";

use Structs\MonitorConnection;

final class Connections
{
    private string $queue_name;
    private array $prev_connections = [];

    public function __construct(string $queue_name)
    {
        $this->queue_name = $queue_name;
    }

    /**
     * Retrieves a list of ip addresses connected to the specified queue
     *
     * @param bool $keep Maintains server connected to in string name
     *                   Ex return: 127.0.0.1:54398 -> 127.0.0.1:5672
     * @return array
     */
    public function getConnections(bool $keep = false): array
    {
        $server_ips = [];

        $connection = RABBITMQ_CONNECTION;
        $auth_token = RABBITMQ_AUTH_TOKEN;
        $url = "http://$connection/api/queues/%2F/{$this->queue_name}";
        $header_opts = [
            "content-type: application/json",
            "authorization: Basic $auth_token",
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url, [
            "headers" => [
                "content-type" => "application/json",
                "authorization" => "Basic $auth_token"
            ]
        ]);
        $body = $response->getBody();

        $data = json_decode($body, true);
        $consumer_details = $data['consumer_details'];
        foreach ($consumer_details as $consumer) {
            $connection_name = $consumer['channel_details']['connection_name'];
            if ($keep === false) {
                $connection_name = explode(" -> ", $connection_name)[0];
            }
            $server_ips[] = $connection_name;
        }

        return $server_ips;
    }

    /**
     * Returns a list of new connections since the last run
     * First run should return all connections as "new", but subsequent runs
     * will show any difference between the runs (added or removed)
     *
     * @return array Array of ConnectionQueues
     */
    public function monitorConnections(): array
    {
        $connections = [];

        $new_conns = $this->getConnections();
        $prev_conns = $this->prev_connections;

        // Getting connected connections
        $incoming_conns = array_diff($new_conns, $prev_conns);
        foreach ($incoming_conns as $connection) {
            $conn = new MonitorConnection($connection, "Connected");
            $connections[] = $conn;
        }

        // Getting disconnected connections
        $outgoing_conns = array_diff($prev_conns, $new_conns);
        foreach ($outgoing_conns as $connection) {
            $conn = new MonitorConnection($connection, "Disconnected");
            $connections[] = $conn;
        }

        // Update previous connection list
        $this->prev_connections = $new_conns;

        return $connections;
    }

    /**
     *
     */
    public function deleteConnections(): bool
    {
        // TODO Drop connections
    }
}
