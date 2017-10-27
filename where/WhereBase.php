<?php

/**
 * @file
 * @brief This file contains the class WhereBase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Base of the WHERE clause clauses.
 */
class WhereBase implements IWhere
{
    
    /**
     * @brief Name of the column.
     * 
     * @var string $columnName
     */
    private $columnName;
    
    /**
     * @brief Value to compare.
     * 
     * @var mixed $value
     */
    private $value;
    
    /**
     * @brief Operator used to compare.
     * 
     * @var WhereOperator $operator
     */
    private $operator;
    
    
    /**
     * @brief Constructs a new instance of the WhereBase class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param WhereOperator $operator
     */
    public function __construct(
        $columnName,
        $value,
        $operator
        )
    {
        if( !is_string( $columnName ) )
            throw new \InvalidScalarTypeException( 'string', $columnName, '$columnName' );
        
        $this->columnName = $columnName;
        
        $this->value = $value;
        
        $this->operator = $operator;
    }
    
    /**
     * @brief Create the where clause.
     * 
     * Create the where clause using a placeholder for the value.
     * The value is added to the $values array.
     * 
     * @param IQueryBuilder $queryBuilder
     * @param[out] array $values Optional
     *   (prepared statement can be executed several times).
     * @retval string
     */
    public function ToString( IQueryBuilder $queryBuilder, array &$values = null )
    {
        if( $queryBuilder !== null )
        {
            $result = $queryBuilder->WhereToString(
                $this->columnName,
                $this->operator,
                $this->value,
                /*ref*/ $values
                );
            
            if( $result !== null )
            {
                return $result;
            }
        }
        
        $values[] = $this->value;
        
        return $queryBuilder->FormatWhere(
            '`' . $this->columnName . '` ' . $this->operator . ' ?' );
    }
    
}
