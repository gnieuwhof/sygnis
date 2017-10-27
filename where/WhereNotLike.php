<?php

/**
 * @file
 * @brief This file contains the class WhereNotLike.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE NOT LIKE clause.
 */
class WhereNotLike extends WhereBase
{

    /**
     * @brief Constructs a new instance of the WhereNotLike class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param WhereOperator $operator
     */
    public function __construct( $columnName, $value )
    {
        // Type checking is done in the base class.
        parent::__construct( $columnName, $value, WhereOperator::NOT_LIKE );
    }

}
