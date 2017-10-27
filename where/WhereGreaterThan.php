<?php

/**
 * @file
 * @brief This file contains the class WhereGreaterThan.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE > clause.
 */
class WhereGreaterThan extends WhereBase
{

    /**
     * @brief Constructs a new instance of the WhereGreaterThan class.
     * 
     * @param string $columnName Name of the column.
     * @param mixed $value Value of the column.
     * @param WhereOperator $operator
     */
    public function __construct( $columnName, $value )
    {
        // Type checking is done in the base class.
        parent::__construct( $columnName, $value, WhereOperator::GREATER_THAN );
    }

}
