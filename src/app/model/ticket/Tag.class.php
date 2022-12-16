<?php

use Adianti\Database\TRecord;

class Tag extends TRecord
{

    const TABLENAME = 'tag';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('description');
        parent::addAttribute('color');
    }
}
