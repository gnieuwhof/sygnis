<?php

/**
 * @file
 * @brief This file contains the class MySqlConfig.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing MySQL specific database setting(s).
 */
class MySqlConfig extends DatabaseConfigBase
{

    /**
     * @brief Returns the MySQL Data Source Name.
     * 
     * @see Config
     * @retval string
     */
    public static /*override*/ function Dsn()
    {
        $host = \Config::MYSQL_HOST;
        $port = \Config::MYSQL_PORT;
        $database = \Config::MYSQL_DATABASE;

        return
            'mysql:' .
            'host=' . $host . ';' .
            'port=' . $port . ';' .
            'dbname=' . $database . ';' .
            'charset=utf8';
    }

}
