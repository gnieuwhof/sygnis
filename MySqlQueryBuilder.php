<?php

/**
 * @file
 * @brief This file contains the class MySqlQueryBuilder.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing functions to create MySQL queries.
 */
class MySqlQueryBuilder implements IQueryBuilder
{
    
    /**
     * @brief Create a query to count from a table.
     *
     * @param string $tableName Name of the table to count rows.
     * @param string $whereClause Query WHERE clause.
     * @retval string Count query.
     * 
     * @code
     * $query = IQueryBuilder->CountRows( 'table', '`id` = 5' );
     * // SELECT COUNT(*) FROM `table` WHERE `id` = 5;
     * @endcode
     */
    public function CountRows( $tableName, $whereClause )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_string( $whereClause ) )
            throw new \InvalidScalarTypeException( 'string', $whereClause, '$whereClause' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( $whereClause == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$whereClause' );
        
        $query =
            'SELECT COUNT(*)' . PHP_EOL .
            'FROM `' . $tableName . '`';
        
        if( $whereClause != 'TRUE' )
        {
            $query .= PHP_EOL .
                'WHERE ' . $whereClause;
        }
        
        return $query . ';';
    }
    
    /**
     * @brief Create a query to delete from a table.
     *
     * @param string $tableName Name of the table to delete from.
     * @param string $whereClause Query WHERE clause.
     * @retval string Delete query.
     * 
     * @code
     * $query = IQueryBuilder->Delete( 'table', '`id` = 5' );
     * // DELETE FROM `table` WHERE `id` = 5;
     * @endcode
     */
    public function Delete( $tableName, $whereClause )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_string( $whereClause ) )
            throw new \InvalidScalarTypeException( 'string', $whereClause, '$whereClause' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( $whereClause == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$whereClause' );
        
        $query =
            'DELETE' . PHP_EOL .
            'FROM `' . $tableName . '`';
        
        if( $whereClause != 'TRUE' )
        {
            $query .= PHP_EOL .
                'WHERE ' . $whereClause;
        }
        
        return $query . ';';
    }
    
    /**
     * @brief Create a query to insert into a table (prepared).
     *
     * @see prepareColumnsForQuery()
     * @param string $tableName Name of the table to insert in.
     * @param string[] $columnNames Array containing the column name(s).
     * @param int $rowCount The number of rows to insert.
     * Used to determine the number of placeholders
     * @retval string Prepared insert query.
     * 
     * @code
     * $query = IQueryBuilder->Insert(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     2
     *     );
     * // INSERT INTO `table` (`col1`, `col2`) VALUES (?, ?),(?, ?);
     * @endcode
     */
    public function Insert(
        $tableName,
        array $columnNames,
        $rowCount = 1
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_int( $rowCount ) )
            throw new \InvalidScalarTypeException( 'int', $rowCount, '$rowCount' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$columnNames' );
        if( $rowCount <= 0 )
            throw new \ArgumentException( 'Value must be greater than zero', '$rowCount' );
        
        $preparedColumnNames = self::PrepareColumnsForQuery( $columnNames );
        
        $rowPlaceholders = '';

        $i = count( $columnNames );
        while( --$i >= 0 )
        {
            $rowPlaceholders .= '?, ';
        }
        
        $rowPlaceholders = '(' . rtrim( $rowPlaceholders, ', ' ) . ')';

        $allPlaceholders = $rowPlaceholders;
        
        while( --$rowCount > 0 )
        {
            $allPlaceholders .= ',' . $rowPlaceholders;
        }
        
        return
            'INSERT INTO `' . $tableName . '`' . PHP_EOL .
            '(' . $preparedColumnNames . ')' . PHP_EOL .
            'VALUES' . PHP_EOL .
            $allPlaceholders . ';';
    }
    
    /**
     * @brief Create a query to insert ignore into a table (prepared).
     *
     * @see Insert()
     * @param string $tableName Name of the table to insert in.
     * @param string[] $columnNames Array containing the column name(s).
     * @param int $rowCount The number of rows to insert.
     *   Used to determine the number of placeholders.
     * @retval string Prepared insert query.
     * 
     * @code
     * $query = IQueryBuilder->Insert(
     *     'table',
     *     array( 'col1', 'col2' )
     *     );
     * // INSERT IGNORE INTO `table` (`col1`, `col2`) VALUES (?, ?);
     * @endcode
     */
    public function InsertIgnore( $tableName, array $columnNames, $rowCount = 1 )
    {
        $query = $this->Insert( $tableName, $columnNames, $rowCount );
        
        // Replace 11 chars starting from pos 0 (which is INSERT INTO)
        // with INSERT IGNORE INTO and return query.
        return substr_replace( $query, 'INSERT IGNORE INTO', 0, 11);
    }
    
    /**
     * @brief Create a query to merge row(s) into a table (prepared).
     * 
     * @see Insert()
     * @param string $tableName Name of the table.
     * @param string[] $columnNames Array containing column name(s).
     * @param string $columnsToIgnoreOnUpdate
     *   String or array of the column(s) to ignore on update.
     * @retval string Prepared merge query.
     * 
     * @code
     * $query = IQueryBuilder->Merge(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     'col2'
     *     );
     * // INSERT INTO `table` (`col1`, `col2`) VALUES (?, ?)
     * // ON DUPLICATE KEY UPDATE `col1` = VALUES(`col1`);
     * @endcode
     */
    public function Merge(
        $tableName,
        array $columnNames,
        $rowCount = 1,
        array $columnsToIgnoreOnUpdate = null
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_int( $rowCount ) )
            throw new \InvalidScalarTypeException( 'int', $rowCount, '$rowCount' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$columnNames' );
        if( $rowCount <= 0 )
            throw new \ArgumentException( 'Value must be greater than zero', '$rowCount' );
        
        $query = $this->Insert( $tableName, $columnNames, $rowCount );
        
        $query = rtrim( $query, ';' ) . PHP_EOL;
        $query .= 'ON DUPLICATE KEY' . PHP_EOL;
        $query .= 'UPDATE' . PHP_EOL;
        
        if( $columnsToIgnoreOnUpdate === null )
        {
            $updateColumns = $columnNames;
        }
        else
        {
            // Remove ignore columns from column names.
            $updateColumns = array_diff(
                $columnNames,
                $columnsToIgnoreOnUpdate
                );
        }
        
        foreach( $updateColumns as $column )
        {            
            $query .=
                '`' . $column . '` = VALUES(`' . $column . '`),' . PHP_EOL;
        }
        
        return trim( $query, '),' . PHP_EOL ) . ');';
    }
    
    /**
     * @brief Creteates a one table select query (no joins).
     * 
     * @see prepareColumnsForQuery()
     * @param sting $tableName The table to select data from.
     * @param string[] $columnNames Array containing column name(s).
     * @param string $whereClause WHERE part of the query.
     * @param bool $isInner Whether new lines and semicolon must be omitted.
     * @retval string Select query.
     * 
     * @code
     * $query = IQueryBuilder->Select(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     'TRUE'
     *     );
     * // SELECT (`col1`, `col2`) FROM 'table' WHERE TRUE;
     * @endcode
     */
    public function Select( $tableName, array $columnNames, $whereClause, $isInner = false )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_string( $whereClause ) )
            throw new \InvalidScalarTypeException( 'string', $whereClause, '$whereClause' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$columnNames' );
        if( $whereClause == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$whereClause' );
        
        $preparedColumnNames = self::PrepareColumnsForQuery( $columnNames );
        
        $separator = ($isInner ? ' ' : PHP_EOL);
        
        $query =
            'SELECT ' . $preparedColumnNames . $separator .
            'FROM `' . $tableName . '`';
        
        if( $whereClause != 'TRUE' )
        {
            $query .= $separator .
                'WHERE ' . $whereClause;
        }
        
        return $query . ($isInner ? '' : ';');
    }
    
    /**
     * @brief Creates a one table select limit query (no joins).
     * 
     * @see Select()
     * @param sting $tableName The table to select data from.
     * @param string[] $columnNames Array containing column name(s).
     * @param string $whereClause WHERE part of the query.
     * @param int $offset Limit offset.
     * @param int $count Limit count.
     * @retval string Select range query.
     * 
     * @code
     * $query = IQueryBuilder->SelectRange(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     TRUE,
     *     4,
     *     2
     *     );
     * // SELECT (`col1`, `col2`) FROM 'table' WHERE TRUE LIMIT 2, 4;
     * @endcode
     */
    public function SelectRange(
        $tableName,
        array $columnNames,
        $whereClause,
        $offset,
        $count
        )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_string( $whereClause ) )
            throw new \InvalidScalarTypeException( 'string', $whereClause, '$whereClause' );
        if( !is_int( $count ) )
            throw new \InvalidScalarTypeException( 'int', $count, '$count' );
        if( !is_int( $offset ) )
            throw new \InvalidScalarTypeException( 'int', $offset, '$offset' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$columnNames' );
        if( $whereClause == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$whereClause' );
        if( $count < 0 )
            throw new \ArgumentException( 'Value cannot be negative', '$count' );
        if( $offset < 0 )
            throw new \ArgumentException( 'Value cannot be negative', '$offset' );
        
        $query = $this->Select( $tableName, $columnNames, $whereClause );
        
        // Add LIMIT to the query.
        return
            rtrim( $query, ';' ) . PHP_EOL .
            'LIMIT ' . $offset . ', ' . $count . ';';
    }

    /**
     * @brief Creates a one table select top query (no joins).
     * 
     * This function basically call SelectRange with an offset of 0.
     * 
     * @see SelectRange()
     * @param sting $tableName The table to select data from.
     * @param string[] $columnNames Array containing column name(s).
     * @param string $whereClause WHERE part of the query.
     * @param int $count Limit count.
     * @retval string Select top query.
     * 
     * @code
     * $query = IQueryBuilder->SelectTop(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     TRUE,
     *     7
     *     );
     * // SELECT (`col1`, `col2`) FROM 'table' LIMIT 7;
     * @endcode
     */
    public function SelectTop(
        $tableName,
        array $columnNames,
        $whereClause,
        $count
        )
    {
        return $this->SelectRange(
            $tableName,
            $columnNames,
            $whereClause,
            0,
            $count
            );
    }
    
    /**
     * @brief Combine table, columns and where clause into UPDATE query.
     * 
     * @param string $tableName Name of the table.
     * @param string[] $columnNames Array containing column name(s).
     * @param string $whereClause Where clause.
     * @retval string Update query.
     * 
     * @code
     * $query = IQueryBuilder->Update(
     *     'table',
     *     array( 'col1', 'col2' ),
     *     '(id > 10)'
     *     );
     * // UPDATE `table` SET `col1` = ?, `col2` = ? WHERE (id > 10);
     * @endcode
     */
    public function Update( $tableName, array $columnNames, $whereClause )
    {
        if( !is_string( $tableName ) )
            throw new \InvalidScalarTypeException( 'string', $tableName, '$tableName' );
        if( !is_string( $whereClause ) )
            throw new \InvalidScalarTypeException( 'string', $whereClause, '$whereClause' );
        
        if( $tableName == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$tableName' );
        if( count( $columnNames ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$columnNames' );
        if( $whereClause == '' )
            throw new \ArgumentException( 'Argument cannot be empty', '$whereClause' );
        
        $set = '';

        foreach( $columnNames as $columnName )
        {
            if( !is_string( $columnName ) )
                throw new \InvalidScalarTypeException( 'string', $columnName, '$columnName' );
            
            $set .= '`' . $columnName . '` = ?, ';
        }
        
        $trimmedSet = rtrim( $set, ', ' );

        return
            'UPDATE `' . $tableName . '`' . PHP_EOL .
            'SET ' . $trimmedSet . PHP_EOL .
            'WHERE ' . $whereClause . ';';
    }
    
    
    /**
     * @brief Surround column names with backticks and concat using a comma.
     * 
     * @param string[] $columnNames Array containing column name(s) or *.
     * @retval string Columnnames surrounded with backticks and separated with comma.
     * 
     * @code
     * $query = IQueryBuilder->prepareColumnsForQuery(
     *     array( 'col1', 'col2' )
     *     );
     * // `col1`, `col2`
     * @endcode
     */
    private static function PrepareColumnsForQuery( array $columnNames )
    {
if( DEVELOPMENT )
{
        assert( count( $columnNames ) > 0 );
}
#endif //DEVELOPMENT
        
        if( reset( $columnNames ) == '*' )
        {
            return '*';
        }
        
        return '`' . implode( '`, `', $columnNames ) . '`';
    }
    
    /**
     * @brief This function is used to overload the WhereBase->ToString().
     * 
     * @param string $columnName
     * @param WhereOperator $operator
     * @param mixed $value
     * @param array $values
     */
    public function WhereToString(
        $columnName,
        $operator,
        $value,
        array &$values = null
        )
    {
        if( $value === null )
        {
            // In MySQL queries we cannot use arithmetic
            // comparison to test for null.
            if( $operator == WhereOperator::EQUAL )
            {
                return '(`' . $columnName . '` IS NULL)';
            }
            else if( $operator == WhereOperator::NOT_EQUAL )
            {
                return '(`' . $columnName . '` IS NOT NULL)';
            }
        }
        
        return null;
    }

    /**
     * @brief Surround the where condition in parenthesis.
     * 
     * @param string $condition
     * @return string
     * 
     * @code
     * $where = IQueryBuilder->FormatWhere(
     *     '`Number` < ?'
     *     );
     * // (`Number` < ?)
     * @endcode
     */
    public function FormatWhere( $condition )
    {
        return '(' . $condition . ')';
    }
    
}
