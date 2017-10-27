<?php

/**
 * @file
 * @brief This file contains the interface IWhere.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Interface for query WHERE classes.
 */
interface IWhere
{
    
    public function ToString( IQueryBuilder $queryBuilder, array &$values );
    
}
