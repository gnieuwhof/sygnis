<?php

/**
 * @file
 * @brief This file contains the class SafePDO.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class preventing uncaught PDO exceptions.
 */
class SafePDO extends \PDO
{

    /**
     * @brief Data Source Name.
     * 
     * @var string $dsn
     */
    private $dsn;
    
    /**
     * @brief Database account username.
     * 
     * @var string $username
     */
    private $username;
    
    /**
     * @brief Database account password.
     * 
     * @var string $password
     */
    private $password;

    
    /**
     * @brief Constructs a new instance of the SafePDO class.
     * 
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct($dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        
        // Do not call parent constructor (use Init() instead).
    }

    /**
     * @brief Init PDO.
     *
     * This function calls the PDO constructor.
     * (Hides sensitive information from PDO exceptions)
     * @throws Exception throws only the message of the PDO exception.
     */
    public function Init()
    {
        try
        {
            parent::__construct(
                $this->dsn,
                $this->username,
                $this->password
                );
            
            // Set attributes after calling Init().
        }
        catch( \PDOException $exception )
        {
            throw new \Exception( $exception->getMessage() );
        }
    }
    
}
