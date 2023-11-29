<?php

namespace Illuminate\Database;

use Support\PDOProxy\PDOPRoxyPoolMap;
use Worker\Built\JsonRpc\JsonRpcClient;

class ConnectionHook extends Connection
{
    /**
     * @param string $query
     * @param array  $bindings
     * @param true   $useReadPdo
     * @return array|mixed
     */
    public function select($query, $bindings = [], $useReadPdo = true): mixed
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }
            return JsonRpcClient::getInstance()->call(
                PDOPRoxyPoolMap::$pools[$this->getDatabaseName()]->range()->name,
                'prepare',
                $query, $bindings
            );
        });
    }
}
