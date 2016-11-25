<?php


namespace Data\Table;

interface ForeignKeyInterface
{
    public function __construct($name, $table, $col = 'id', $key = null);
}
