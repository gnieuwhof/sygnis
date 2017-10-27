<?php

/**
 * @file
 * @brief This file contains the class DatabaseConfigBase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing basic database settings.
 */
abstract class DatabaseConfigBase implements IPdoConfig
{

    /**
     * @brief Returns the username of the database account.
     * 
     * @see Config::MYSQL_USERNAME
     * @retval string
     */
    public static function Username()
    {
        return \Config::MYSQL_USERNAME;
    }

    /**
     * @brief Returns the password of the database account.
     * 
     * @see Config::MYSQL_PASSWORD
     * @retval string
     */
    public static function Password()
    {
        return \Config::MYSQL_PASSWORD;
    }

}
