<?php

/**
 * @file
 * @brief This file contains the class WhereOperator.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class containing SQL WHERE operators.
 */
class WhereOperator
{

    /**
     * @var string EQUAL
     */
    const EQUAL = '=';

    /**
     * @var string NOT_EQUAL
     */
    const NOT_EQUAL = '<>';
    
    /**
     * @var string GREATER_THAN
     */
    const GREATER_THAN = '>';
    
    /**
     * @var string LESS_THAN
     */
    const LESS_THAN = '<';
    
    /**
     * @var string GREATER_THAN_OR_EQUAL
     */
    const GREATER_THAN_OR_EQUAL = '>=';
    
    /**
     * @var string LESS_THAN_OR_EQUAL
     */
    const LESS_THAN_OR_EQUAL = '<=';
    
    /**
     * Not supported because of different database implementations.
     * Use GREATER_THAN AND LESS_THAN instead.
     */
    //const BETWEEN = 'BETWEEN';
    
    /**
     * Search for a pattern.
     * 
     * @var string LIKE
     */
    const LIKE = 'LIKE';
    
    /**
     * Search for a pattern
     * 
     * @var string NOT_LIKE
     */
    const NOT_LIKE = 'NOT LIKE';
    
    // Use QueryBuilder->WhereIn() instead.
    //const IN = 'IN';

}
