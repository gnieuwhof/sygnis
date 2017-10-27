<?php

/**
 * @file
 * @brief This file contains the class WhereInnerSelect.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE ... (SELECT ... FROM ...) clause.
 */
class WhereInnerSelect implements IWhere
{
    
    /**
     * @brief Name of the table.
     * 
     * @var string $tableName
     */
    private $tableName;
    
    /**
     * @brief Name of the column.
     * 
     * @var string $columnName
     */
    private $columnName;
    
    /**
     * @brief Operator used to combine the clauses.
     * 
     * @var BoolOperator $operator
     */
    private $operator;
    
    /**
     * @brief Name of the inner select column.
     * 
     * @var string $innerColumnName
     */
    private $innerColumnName;
    
    /**
     * @brief Clause to create inner select.
     * 
     * @var IWhere $clause
     */
    private $clause;
    
    
    /**
     * @brief Constructs a new instance of the WhereInnerSelect class.
     * 
     * @param string $tableName Name of the table.
     * @param string $columnName Name of the column.
     * @param WhereOperator $operator.
     * @param string $innerColumnName Name of the inner select column.
     * @param IWhere $clause inner select clause.
     */
    public function __construct( $tableName, $columnName, $operator, $innerColumnName, $clause )
    {
        if( !is_string( $columnName ) )
            throw new \InvalidScalarTypeException( 'string', $columnName, '$columnName' );
        
        $this->tableName = $tableName;
        
        $this->columnName = $columnName;
        
        $this->operator = $operator;
        
        $this->innerColumnName = $innerColumnName;
        
        $this->clause = $clause;
    }
    
    
    /**
     * @brief Create the where inner select clause.
     * 
     * Create the where inner select clause using placeholders for the values.
     * The values are added to the $values array.
     * 
     * @param IQueryBuilder $queryBuilder
     * @param[out] array $values
     * @retval string
     */
    public function ToString( IQueryBuilder $queryBuilder, array &$values )
    {
        $innerWhere = $this->clause->ToString( $queryBuilder, /*ref*/ $values );
        
        $innerSelect = $queryBuilder->Select(
            $this->tableName, array( $this->innerColumnName ), $innerWhere, true );
        
        return $queryBuilder->FormatWhere(
            '`' . $this->columnName . '` ' . $this->operator .
            ' (' . $innerSelect . ')'
            );
    }
    
}
