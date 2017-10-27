<?php

/**
 * @file
 * @brief This file contains the class MicrosoftSqlConfig.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing Microsoft SQL specific database setting(s).
 */
class MicrosoftSqlConfig extends DatabaseConfigBase
{

    /**
     * @brief Returns the Microsoft SQL Data Source Name.
     * 
     * @see Config
     * @retval string
     */
    public static /*override*/ function Dsn()
    {
        $host = \Config::MS_SQL_HOST;
        $database = \Config::MS_SQL_DATABASE;
        $username = \Config::MS_SQL_USERNAME;
        $password = \Config::MS_SQL_PASSWORD;

        return
            'odbc:Driver={SQL Server Native Client 10.0};Server=' . $host .
            ';' . 'Database=' . $database .
            ';Uid=' . $username . ';Pwd=' . $password;
    }

}
