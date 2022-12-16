<?php

use Adianti\Database\TRecord;

class ProjectMember extends TRecord
{

    const TABLENAME = 'project_member';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('project_id');
        parent::addAttribute('user_id');
    }
}
