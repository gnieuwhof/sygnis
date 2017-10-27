<?php

/**
 * @file
 * @brief This file contains the interface IPdoConfig.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Interface for PDO config classes.
 */
interface IPdoConfig
{

    /**
     * @brief Returns the username of the database account.
     *
     * @retval string
     */
    static function Username();

    /**
     * @brief Returns the password of the database account.
     *
     * @retval string
     */
    static function Password();

    /**
     * @brief Returns the database Data Source Name.
     *
     * @retval string
     */
    static function Dsn();

}
