<?php

use Adianti\Database\TRecord;

class Project extends TRecord
{

    const TABLENAME = 'project';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('description');
    }

    public static function getUserProjects(SystemUser $user)
    {
        $member_projects = ProjectMember::where('user_id', '=', $user->id)->load();

        if (!$member_projects) {
            return [];
        }

        $projects = array();
        foreach ($member_projects as $member_project) {
            $project = new Project($member_project->project_id);
            $projects[$member_project->project_id] = $project;
        }
        return $projects;
    }


    /**
     * Add a program to the user
     * @param $object Instance of SystemUser
     */
    public function addMember(SystemUser $systemUser)
    {
        $object = new ProjectMember;
        $object->user_id = $systemUser->id;
        $object->project_id = $this->id;
        $object->store();
    }


    public function getMembers()
    {
        return parent::loadAggregate('SystemUser', 'ProjectMember', 'project_id', 'user_id', $this->id);
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        // delete the related objects
        ProjectMember::where('project_id', '=', $this->id)->delete();
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_groupSystem_program objects
        $id = isset($id) ? $id : $this->id;

        ProjectMember::where('project_id', '=', $id)->delete();

        // delete the object itself
        parent::delete($id);
    }
}
