<?php

/**
 * @file
 * @brief This file contains the interface IDatabase.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Interface for DBMS classes.
 * 
 * Classes implementing this interface usually
 * construct a SafePDO object and an IQueryBuilder object.
 */
interface IDbmsAdapter
{
    
    public function GetDbh();
    
    public function GetQueryBuilder();
    
}
