<?php

use Adianti\Database\TRecord;

class IssueWatcher extends TRecord
{

    const TABLENAME = 'issue_watcher';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('issue_id');
        parent::addAttribute('user_id');
    }
}
