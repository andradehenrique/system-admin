<?php

use Adianti\Database\TRecord;

class Priority extends TRecord
{

    const TABLENAME = 'priority';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('description');
        parent::addAttribute('color');
    }
}
