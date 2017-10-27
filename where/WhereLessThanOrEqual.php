<?php

/**
 * @file
 * @brief This file contains the class WhereLessThanOrEqual.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE <= clause.
 */
class WhereLessThanOrEqual extends WhereBase
{

    /**
     * @brief Constructs a new instance of the WhereLessThanOrEqual class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param WhereOperator $operator
     */
    public function __construct( $columnName, $value )
    {
        // Type checking is done in the base class.
        parent::__construct(
            $columnName,
            $value,
            WhereOperator::LESS_THAN_OR_EQUAL
            );
    }

}
