<?php

/**
 * @file
 * @brief This file contains the class WhereTrue.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Class used to create WHERE TRUE clause.
 */
class WhereTrue implements IWhere
{
    
    /**
     * @inheritdoc
     */
    public function ToString( IQueryBuilder $queryBuilder, array &$values = null )
    {
        return 'TRUE';
    }
    
}
