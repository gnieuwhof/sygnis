<?php

/**
 * @file
 * @brief This file contains the class BoolAnd.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to AND combine where clauses.
 */
class BoolAnd extends BoolBase
{
    
    /**
     * @brief Constructs a new instance of the BoolAnd class.
     * 
     * @param IWhere[] $clauses Clauses to combine.
     */
    public function __construct( array $clauses )
    {
        parent::__construct( $clauses, BoolOperator::$AND );
    }
    
}
