<?php

/**
 * @file
 * @brief This file contains the class BoolBase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to combine where clauses.
 */
class BoolBase implements IWhere
{
    
    /**
     * @brief Array containing clauses to combine.
     * 
     * @var IWhere[] $clauses
     */
    private $clauses;
    
    /**
     * @brief Operator used to combine the clauses.
     * 
     * @var BoolOperator $operator
     */
    private $operator;
    
    
    /**
     * @brief Constructs a new instance of the BoolBase class.
     * 
     * @param IWhere[] $clauses Clauses to combine.
     * @param BoolOperator $operator Operator used to combine the clauses.
     */
    public function __construct( array $clauses, $operator )
    {
        if( count( $clauses ) == 0 )
            throw new \ArgumentException( 'Array must at least contain one element', '$clauses' );
        
        $this->clauses = $clauses;
        
        $this->operator = $operator;
    }

    
    /**
     * @brief Combine clauses.
     * 
     * Combines the clauses using the constructor passed in operator.
     * Surrounds the combined clauses in parenthesis.
     * 
     * @param[out] mixed[] $values
     * While creating the string all the values are added to this array.
     * @retval string
     */
    public function ToString( IQueryBuilder $queryBuilder, array &$values )
    {
        $result = '';
        
        $operator = ' ' . $this->operator . ' ';
        
        foreach( $this->clauses as $clause )
        {
            $result .= self::IWhereSafeToString( $queryBuilder, $clause, /*ref*/ $values ) .
                $operator;
        }
        
        // Remove operator from the end and enclose in parentheses.
        return $queryBuilder->FormatWhere( rtrim( $result, $operator ) );
    }
    
    /**
     * @brief Function to perform type checking on the where clause.
     * 
     * @param IWhere $iWhere
     * @param[out] mixed[] $values
     * @return string
     */
    private static function IWhereSafeToString(
        IQueryBuilder $queryBuilder, IWhere $iWhere, array &$values )
    {
        return $iWhere->ToString( $queryBuilder, /*ref*/ $values );
    }
    
}
