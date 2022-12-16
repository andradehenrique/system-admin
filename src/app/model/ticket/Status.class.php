<?php

use Adianti\Database\TRecord;

class Status extends TRecord
{

    const TABLENAME = 'status';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('description');
        parent::addAttribute('color');
        parent::addAttribute('final_status');
        parent::addAttribute('order_number');
    }
}
