<?php

/**
 * @file
 * @brief This file contains the class MySqlDatabase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief This is the MySQL Database class.
 * 
 * The only goal of this class is to construct
 * SafePDO and IQueryBuiler objects.
 * 
 * Do not put any logic in this class, or any subclasses, that
 * retrieves or manipulates database entities.
 */
class MySqlDatabase implements IDbmsAdapter
{

    /**
     * @brief Database handle.
     * 
     * @var SafePDO $dbh
     */
    private $dbh = null;
    
    /**
     * @brief DBMS specific QueryBuilder object.
     * 
     * @var IQueryBuilder $queryBuilder
     */
    private $queryBuilder = null;


    /**
     * @brief Constructs a new instance of the MySqlDatabase class.
     * 
     * Make sure this constructor is called from
     * the child constructor if it implements one!
     */
    public function __construct()
    {
        $username = MySqlConfig::Username();
        $password = MySqlConfig::Password();
        $dsn = MySqlConfig::Dsn();

        $this->dbh = new SafePDO(
            $dsn,
            $username,
            $password
            );
        
        $this->dbh->Init();
        
        $this->dbh->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );

        $this->dbh->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_NUM
            );

        $this->dbh->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
    }
    
    /**
     * @brief Gets the database handle.
     * 
     * @retval SafePDO
     */
    public function GetDbh()
    {
        return $this->dbh;
    }
    
    /**
     * @brief Gets the MySqlQueryBuilder instance (lazy).
     * 
     * @retval IQueryBuilder
     */
    public function GetQueryBuilder()
    {
        if( $this->queryBuilder == null )
        {
            $this->queryBuilder = new MySqlQueryBuilder();
        }
        
        return $this->queryBuilder;
    }
    
}
