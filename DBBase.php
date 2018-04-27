<?php

/**
 * @file
 * @brief This file contains the class DBBase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief DB Base class.
 */
class DBBase
{

    /**
     * @brief Execute a query and return the number of affected rows
     *   using prepared statement.
     *
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[][] $data Array of arrays containing placeholder data.
     * @retval int[] The number of affected rows.
     */
    protected static function MultiExecute(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
}
#endif //DEVELOPMENT
        
        $dbh = $dbmsAdapter->GetDbh();
        $stmt = $dbh->prepare( $query );
        
        $affectedRows = array();
        
        foreach( $data as $rowData )
        {
            if( !is_array( $rowData ) )
            {
                $rowData = array( $rowData );
            }
            
if( DEVELOPMENT )
{
            if( count( $rowData ) > 0 )
                assert( array_keys( $rowData ) === range( 0, count( $rowData ) - 1 ) );
}
#endif //DEVELOPMENT
            
            $stmt->execute( $rowData );
            
            $affectedRows[] = $stmt->rowCount();
        }
        
        return $affectedRows;
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
     * @param mixed[] $data Array containing query values (optional).
     * @retval PDOStatement
     */
    protected static function Execute(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
        if( count( $data ) > 0 )
            assert( array_keys( $data ) === range( 0, count( $data ) - 1 ) );
}
#endif //DEVELOPMENT

        $dbh = $dbmsAdapter->GetDbh();
        $stmt = $dbh->prepare( $query );
        
        for( $i = 0; $i < count( $data ); ++$i )
        {
            $binary = null;
            
            if( is_array( $data[$i] ) )
            {
                $binary = $data[$i];
            }
            else if( is_object( $data[$i] ) )
            {
                $binary = self::GetFirstArray( $data[$i] );
            }
            else
            {
                continue;
            }
            
            $data[$i] = implode( array_map( 'chr', $binary ) );
                
            $stmt->bindParam( $i + 1, $data[$i], \PDO::PARAM_LOB );
        }
        
        $stmt->execute($data);
        
        return $stmt;
    }
    
    /**
     * @brief Returns the first array member of the given object.
     * 
     * @param object $object
     * @return mixed[]
     */
    private static function GetFirstArray( $object )
    {
        $objectArray = (array)$object;
                
        foreach( $objectArray as $member )
        {
            if( is_array( $member ) )
            {
                return $member;
            }
        }
        
        return null;
    }
    
    /**
     * @brief Execute query and return all rows.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @return mixed[][]
     */
    protected static function ExecuteCollectionReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
}
#endif //DEVELOPMENT

        $stmt = self::Execute( $dbmsAdapter, $query, $data );
        
        return $stmt->fetchAll();
    }
    
    /**
     * @brief Execute query and return the first row.
     * 
     * @param IDbmsAdapter $dbmsAdapter
     * @param string $query The query to execute.
     * @param string[] $data Array containing query values (optional).
     * @return mixed[][]
     */
    protected static function ExecuteRowReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
}
#endif //DEVELOPMENT

        $stmt = self::Execute( $dbmsAdapter, $query, $data );
        
        return $stmt->fetch();
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
    protected static function ExecuteCellReader(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array(),
        $columnNumber = 0
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
        assert( is_int( $columnNumber ) );
}
#endif //DEVELOPMENT

        $stmt = self::Execute( $dbmsAdapter, $query, $data );
        
        return $stmt->fetchColumn( $columnNumber );
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
    protected static function ExecuteNonQuery(
        IDbmsAdapter $dbmsAdapter,
        $query,
        array $data = array()
        )
    {
if( DEVELOPMENT )
{
        assert( is_string( $query ) );
}
#endif //DEVELOPMENT

        $stmt = self::Execute( $dbmsAdapter, $query, $data );
        
        return $stmt->rowCount();
    }
    
}
