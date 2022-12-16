<?php

use Adianti\Database\TRecord;
use App\Helpers\FileHelper;

class Issue extends TRecord
{
    const TABLENAME = 'issue';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    const CREATEDAT = 'created_at';
    const UPDATEDAT = 'updated_at';

    use SystemChangeLogTrait;

    private ?Project $project;
    private ?Priority $priority;
    private ?Category $category;
    private ?Status $status;
    private ?SystemUser $user;
    private ?SystemUser $assigned;
    private ?self $related_issue;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('solution');
        parent::addAttribute('project_id');
        parent::addAttribute('priority_id');
        parent::addAttribute('category_id');
        parent::addAttribute('status_id');
        parent::addAttribute('assigned_id');
        parent::addAttribute('user_id');
        parent::addAttribute('related_issue_id');
        parent::addAttribute('finished_at');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    public function get_project(): ?Project
    {
        if (empty($this->project))
            $this->project = new Project($this->project_id);

        return $this->project;
    }

    public function get_priority(): ?Priority
    {
        if (empty($this->priority))
            $this->priority = new Priority($this->priority_id);

        return $this->priority;
    }

    public function get_category(): ?Category
    {
        if (empty($this->category))
            $this->category = new Category($this->category_id);

        return $this->category;
    }

    public function get_status(): ?Status
    {
        if (empty($this->status))
            $this->status = new Status($this->status_id);

        return $this->status;
    }

    public function get_user(): ?SystemUser
    {
        if (empty($this->user))
            $this->user = new SystemUser($this->user_id);

        return $this->user;
    }

    public function get_assigned(): ?SystemUser
    {
        if (empty($this->assigned))
            $this->assigned = new SystemUser($this->assigned_id);

        return $this->assigned;
    }

    public function get_related_issue(): ?self
    {
        if (empty($this->related_issue))
            $this->related_issue = new self($this->related_issue_id);

        return $this->related_issue;
    }

    public function addWatcher(SystemUser $systemUser)
    {
        $object = new IssueWatcher;
        $object->user_id = $systemUser->id;
        $object->issue_id = $this->id;
        $object->store();
    }

    public function getWatchers()
    {
        return parent::loadAggregate('SystemUser', 'IssueWatcher', 'issue_id', 'user_id', $this->id);
    }

    public function getFiles()
    {
        return parent::loadComposite('IssueFile', 'issue_id', $this->id);
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        // delete the related objects
        IssueWatcher::where('issue_id', '=', $this->id)->delete();
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_groupSystem_program objects
        $id = isset($id) ? $id : $this->id;

        IssueWatcher::where('issue_id', '=', $id)->delete();

        $files = $this->getFiles();
        if ($files) {
            FileHelper::delTree("files/tickets/issues/$id");
            IssueFile::where('issue_id', '=', $id)->delete();
        }

        // delete the object itself
        parent::delete($id);
    }
}
