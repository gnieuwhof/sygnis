<?php

/**
 * @file
 * @brief This file contains the class BoolOr.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to OR combine where clauses.
 */
class BoolOr extends BoolBase
{
    
    /**
     * @brief Constructs a new instance of the BoolOr class.
     * 
     * @param IWhere[] $clauses Clauses to combine.
     */
    public function __construct( array $clauses )
    {
        parent::__construct( $clauses, BoolOperator::$OR );
    }
    
}
