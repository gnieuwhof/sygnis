<?php

/**
 * @file
 * @brief This file contains the class WhereNotEqual.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE <> clause.
 */
class WhereNotEqual extends WhereBase
{

    /**
     * @brief Constructs a new instance of the WhereNotEqual class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param IQueryBuilder $queryBuilder
     */
    public function __construct( $columnName, $value )
    {
        // Type checking is done in the base class.
        parent::__construct(
            $columnName,
            $value,
            WhereOperator::NOT_EQUAL
            );
    }

}
