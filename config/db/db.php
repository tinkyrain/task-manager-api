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
            'host' => 'localhost',
            'user' => 'root',
            'password' => 'root',
            'database' => 'task_manager',
        ];
    }
}
