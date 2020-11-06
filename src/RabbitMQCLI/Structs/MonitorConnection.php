<?php

namespace Structs;

final class MonitorConnection
{
    public string $ip;
    public string $status;

    public function __construct($ip, $status)
    {
        $this->ip = $ip;
        $this->status = $status;
    }
}
