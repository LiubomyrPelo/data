<?php

namespace Data\Table;

class ForeignKey implements ForeignKeyInterface
{
    public $name;
    public $table;
    public $col;
    public $key;

    public function __construct($name, $table, $col = 'id', $key = null)
    {
        $this->name = $name;
        $this->table = $table;
        $this->col = $col;
        $this->key = $key;
    }
}
