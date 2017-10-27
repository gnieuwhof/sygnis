<?php

/**
 * @file
 * @brief This file contains the class WhereLike.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE LIKE clause.
 */
class WhereLike extends WhereBase
{

    /**
     * @brief Constructs a new instance of the WhereLike class.
     * 
     * @link http://www.w3schools.com/sql/sql_like.asp
     * @param string $columnName Name of the column.
     * @param mixed $searchPattern Pattern to search for.
     * @param WhereOperator $operator
     */
    public function __construct( $columnName, $searchPattern )
    {
        // Type checking is done in the base class.
        parent::__construct( $columnName, $searchPattern, WhereOperator::LIKE );
    }

}
