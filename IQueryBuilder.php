<?php

/**
 * @file
 * @brief This file contains the interface IQueryBuilder.
 */

namespace nl\gn\Sygnis;

/**
 * @brief Interface for query builder classes.
 */
interface IQueryBuilder
{
    
    public function CountRows( $table, $where );
    
    public function Delete( $table, $where );
    
    public function Insert( $table, array $columnNames, $rowCount = 1 );
    
    public function InsertIgnore( $table, array $columnNames, $rowCount = 1 );

    public function Merge(
        $table,
        array $columnNames,
        $rowCount = 1,
        array $columnsToIgnoreOnUpdate = null
        );
    
    public function Select( $table, array $columnNames, $where, $isInner = false );

    public function SelectRange( $table, array $columnNames, $where, $start, $count );
    
    public function SelectTop( $table, array $columnNames, $where, $count );
        
    public function Update( $table, array $columnNames, $where );
    
    public function WhereToString( $columnName, $operator, $value, array &$values = null );

    public function FormatWhere( $condition );
    
}
