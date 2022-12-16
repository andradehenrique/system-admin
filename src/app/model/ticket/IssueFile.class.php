<?php

use Adianti\Database\TRecord;

class IssueFile extends TRecord
{

    const TABLENAME = 'issue_file';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('issue_id');
        parent::addAttribute('file_path');
    }
}
