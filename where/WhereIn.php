<?php

/**
 * @file
 * @brief This file contains the class WhereIn.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE IN clause.
 */
class WhereIn implements IWhere
{
    
    /**
     * @brief Name of the column.
     * 
     * @var string $columnName
     */
    private $columnName;
    
    /**
     * @brief Values to compare.
     * 
     * @var mixed $value
     */
    private $values;
    
    
    /**
     * @brief Constructs a new instance of the WhereIn class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param WhereOperator $operator
     */
    public function __construct( $columnName, $values )
    {
        if( !is_string( $columnName ) )
            throw new \InvalidScalarTypeException( 'string', $columnName, '$columnName' );
        
        $this->columnName = $columnName;
        
        $this->values = $values;
    }
    
    
    /**
     * @brief Create the where clause.
     * 
     * Create the where clause using a placeholder for the value.
     * The value is added to the $values array.
     * 
     * @param IQueryBuilder $queryBuilder
     * @param[out] array $values
     * @retval string
     */
    public function ToString( IQueryBuilder $queryBuilder, array &$values )
    {
        $placeholders = '';
        
        foreach( $this->values as $value )
        {
            $placeholders  .= '?, ';
            
            $values[] = $value;
        }
        
        $formattedPlaceholders = $queryBuilder->FormatWhere(
            rtrim( $placeholders, ', ' )
            );
        
        return $queryBuilder->FormatWhere(
            '`' . $this->columnName . '` IN ' . $formattedPlaceholders . ''
            );
    }
    
}
