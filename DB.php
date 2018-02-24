<?php

/**
 * @file
 * @brief This file contains the class DB.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing basic database functions.
 */
class DB extends DBBase
{

    /**
     * @brief Get the number of affected rows.
     *
     * @param PDOStatement $stmt Statement returned from Prepare().
     * @retval int The number of affected rows.
     */
    public static function GetAffectedRowsCount( \PDOStatement $stmt )
    {
        return $stmt->rowCount();
    }

    /**
     * @brief Get the ID (autoincrement column value) of the last inserted row.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $columnName Name of the autoincrement column (optional).
     * @retval int Last inserted ID.
     */
    public static function GetLastInsertId(
        IDbmsAdapter $dbmsAdapter,
        $columnName = null
        )
    {
        if( ( $columnName !== null ) && !is_string( $columnName ) )
            throw new \InvalidScalarTypeException( 'string', $columnName, '$columnName' );
        
        $dbh = $dbmsAdapter->GetDbh();
        return $dbh->lastInsertId( $columnName );
    }
    
    /**
     * @brief Insert data (multiple rows) into a table.
     *
     * @see MultiPreparedExecute()
     * @see IQueryBuilder->PreparedInsert()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $tableName Name of the table.
     * @param string[] $columnNames array( column names ).
     * @param string[][] $columnValues array( array( 2, 4 ), array( 9, 1 ) ).
     * @param bool $ignoreIfExists If true does not insert if already exists.
     * @retval int The number of affected rows.
     */
    public static function Insert(
        IDbmsAdapter $dbmsAdapter,
        $tableName,
        array $columnNames,
        array $columnValues,
        $ignoreIfExists = false
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_bool( $ignoreIfExists ) )
            throw new \InvalidScalarTypeException( 'bool', $ignoreIfExists, '$ignoreIfExists' );
        
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );
        
        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $query = $queryBuilder->Insert(
            $tableName,
            $columnNames,
            count( $columnValues ),
            $ignoreIfExists
            );
        
        return parent::MultiExecute( $dbmsAdapter, $query, $columnValues );
    }
    
    /**
     * @brief Merge (insert/update) data (multiple rows) in(to) a table.
     *
     * @todo handle max_allowed_packet
     * @see MultiPreparedExecute()
     * @see IQueryBuilder->PreparedMerge()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $tableName Name of the table.
     * @param string[] $columnNames array( column names ).
     * @param string[][] $columnValues array( array( 2, 4 ), array( 9, 1 ) ).
     * @param string[] $columnsToIgnoreOnUpdate string/array( column names ).
     * @retval int The number of affected rows.
     */
    public static function Merge(
        IDbmsAdapter $dbmsAdapter,
        $tableName,
        array $columnNames,
        array $columnValues,
        array $columnsToIgnoreOnUpdate = null
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );

        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $query = $queryBuilder->Merge(
            $tableName,
            $columnNames,
            count( $columnValues ),
            $columnsToIgnoreOnUpdate
            );
        
        // Flatten array.
        $values = call_user_func_array( 'array_merge', $columnValues );
        
        return parent::ExecuteNonQuery( $dbmsAdapter, $query, $values );
    }
    
    /**
     * @brief Execute a query and return the number of affected rows
     *   using prepared statement.
     *
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[][] $data Array of arrays containing placeholder data.
     * @retval int[] The number of affected rows.
     */
    public static function MultiExecute(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        
        return parent::MultiExecute( $dbmsAdapter, $query, $data );
    }
    
    /**
     * @brief Count the number of rows corresponding to the where clause.
     * 
     * @see IQueryBuilder->CountRows()
     * @see ExecuteCellReader()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $tableName Name of the table.
     * @param IWhere $where Where clause object.
     * @retval int The number of rows.
     */
    public static function CountRows(
        IDbmsAdapter $dbmsAdapter,
        $tableName,
        IWhere $where
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        
        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );

        $query = $queryBuilder->CountRows( $tableName, $whereClause );

        return parent::ExecuteCellReader( $dbmsAdapter, $query, $whereValues );
    }
    
    /**
     * @brief Delete rows from a table.
     * 
     * @see ExecuteNonQuery()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $tableName Name of the table.
     * @param IWhere $where Where clause object.
     * @retval int The number of affected rows.
     */
    public static function Delete(
        IDbmsAdapter $dbmsAdapter,
        $tableName,
        IWhere $where
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );

        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );

        $query = $queryBuilder->Delete( $tableName, $whereClause );
        
        return parent::ExecuteNonQuery( $dbmsAdapter, $query, $whereValues );
    }

    /**
     * @brief Execute a query and return the PDO statement.
     * 
     * Use the PDO statement to fetch the result.
     *
     * @see errorCheck()
     * @see GetAffectedRowsCount()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @retval PDOStatement
     */
    public static function Execute(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        
        return parent::Execute( $dbmsAdapter, $query, $data );
    }
    
    /**
     * @brief Execute query and return all rows.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @return mixed[][]
     */
    public static function ExecuteCollectionReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        
        return parent::ExecuteCollectionReader( $dbmsAdapter, $query, $data );
    }
    
    /**
     * @brief Execute query and return the first row.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @return mixed[][]
     */
    public static function ExecuteRowReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        
        return parent::ExecuteRowReader( $dbmsAdapter, $query, $data );
    }
    
    /**
     * @brief Execute query and return given (first by default) cell of the first row.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @param int $columnNumber Number of the col from the row (default: first).
     * @return mixed[][]
     */
    public static function ExecuteCellReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array(),
        $columnNumber = 0
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        if( !is_int( $columnNumber ) )
            throw new \InvalidScalarTypeException( 'int', $columnNumber, '$columnNumber' );
        
        if( $columnNumber < 0 )
            throw new \ArgumentValueException( \ExceptionMessages::NegativeValue, '$columnNumber' );
        
        return parent::ExecuteCellReader( $dbmsAdapter, $query, $data, $columnNumber );
    }
    
    
    /**
     * @brief Execute a non query and return the number of affected rows.
     *
     * @see Execute()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @retval int The number of affected rows.
     */
    public static function ExecuteNonQuery(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
        if( !is_string( $query ) )
            throw new \InvalidScalarTypeException( 'string', $query, '$query' );
        
        return parent::ExecuteNonQuery( $dbmsAdapter, $query, $data );
    }
    
    /**
     * @brief Retrieve data from a table.
     *
     * @see IQueryBuilder->Select()
     * @see ExecuteCollectionReader()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string[] $columnNames
     * @param string $tableName Name of the table.
     * @param IWhere $where Where clause object.
     * @param boolean $distinct Whether the result should be distinct.
     * @retval mixed[][]
     */
    public static function RetrieveAll(
        IDbmsAdapter $dbmsAdapter,
        array $columnNames,
        $tableName,
        IWhere $where,
        $distinct = false
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );

        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );
        
        $query = $queryBuilder->Select(
            $tableName,
            $columnNames,
            $whereClause,
            false,
            $distinct
            );
        
        return parent::ExecuteCollectionReader( $dbmsAdapter, $query, $whereValues );
    }
    
    /**
     * @brief Retrieve data from a table.
     * 
     * Return the first row that matches the where clause.
     *
     * @see IQueryBuilder->Select()
     * @see ExecuteRowReader()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string[] $columnNames
     * @param string $tableName Name of the table.
     * @param IWhere $where Where clause object.
     * @retval mixed[]
     */
    public static function RetrieveFirst(
        IDbmsAdapter $dbmsAdapter,
        array $columnNames,
        $tableName,
        IWhere $where
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );

        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );
        
        $query = $queryBuilder->Select(
            $tableName,
            $columnNames,
            $whereClause
            );
        
        return parent::ExecuteRowReader( $dbmsAdapter, $query, $whereValues );
    }
    
    /**
     * @brief Retrieve data from a table.
     *
     * @see IQueryBuilder->SelectRange()
     * @see ExecuteCollectionReader()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string[] $columnNames
     * @param string $tableName Name of the table.
     * @param IWhere $where Where clause object.
     * @param int $start LIMIT offset.
     * @param int $count LIMIT number of rows.
     * @retval mixed[][]
     */
    public static function RetrieveRange(
        IDbmsAdapter $dbmsAdapter,
        array $columnNames,
        $tableName,
        IWhere $where,
        $start,
        $count
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_int( $start ) )
            throw new \InvalidScalarTypeException( 'int', $start, '$start' );
        if( !is_int( $count ) )
            throw new \InvalidScalarTypeException( 'int', $count, '$count' );
        
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );
        if( $start < 0 )
            throw new \ArgumentValueException( \ExceptionMessages::NegativeValue, '$start' );
        if( $count <= 0 )
            throw new \ArgumentValueException( \ExceptionMessages::LowerOrEqualToZeroValue, '$count' );
        
        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );
        
        $query = $queryBuilder->SelectRange(
            $tableName,
            $columnNames,
            $whereClause,
            $start,
            $count
            );

        return parent::ExecuteCollectionReader( $dbmsAdapter, $query, $whereValues );
    }
    
    /**
     * @brief Update data in a table.
     *
     * @see IQueryBuilder->Update()
     * @see ExecuteNonQuery()
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $tableName Name of the table.
     * @param string[] $columnValues array( col => val, col2 => val2 ).
     * @param IWhere $where Where clause object.
     * @retval int The number of affected rows.
     */
    public static function Update(
        IDbmsAdapter $dbmsAdapter,
        $tableName,
        array $columnValues,
        IWhere $where
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        
        if( count( $columnValues ) == 0 )
            throw new \ArgumentException( \ExceptionMessages::EmptyArray, '$columnNames' );

        $columns = array_keys( $columnValues );
        
        $queryBuilder = $dbmsAdapter->GetQueryBuilder();
        $whereValues = array();
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );
        
        $query = $queryBuilder->Update(
            $tableName,
            $columns,
            $whereClause
            );
        
        $values = array_values( $columnValues );
        
        $valuesAndWhereValues =  array_merge( $values, $whereValues );
        
        return parent::ExecuteNonQuery( $dbmsAdapter, $query, $valuesAndWhereValues );
    }
    
}
