<?php

class DB {
    /**
     * This method return Database connection data
     *
     * @return array
     */
    public static function getDBConnection(): array
    {
        return [
            'host' => '127.0.0.1',
            'user' => 'postgres',
            'password' => 'password',
            'database' => 'task-manager',
        ];
    }
}
