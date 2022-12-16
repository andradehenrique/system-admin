<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TForm;
use Adianti\Database\TCriteria;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TExpression;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TMultiFile;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Container\TNotebook;
use Adianti\Validator\TRequiredValidator;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Wrapper\TDBMultiSearch;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapNotebookWrapper;

class UpdateIssueForm extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait;
    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('ticket');    // defines the database
        $this->setActiveRecord('Issue');   // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Issue');
        $this->form->setFormTitle('Chamado');

        // create the form fields
        $id = new TEntry('id');
        $project = new TDBCombo('project_id', $this->database, 'Project', 'id', 'description');
        $priority = new TDBCombo('priority_id', $this->database, 'Priority', 'id', 'description');
        $category = new TDBCombo('category_id', $this->database, 'Category', 'id', 'description');
        $title = new TEntry('title');
        $files = new TMultiFile('files');
        $description = new THtmlEditor('description');
        $solution = new THtmlEditor('solution');
        $status = new TDBCombo('status_id', $this->database, 'Status', 'id', 'description');
        $related_issue = new TDBUniqueSearch('related_issue_id', $this->database, 'Issue', 'id', 'title');

        $criteria = new TCriteria;
        $criteria->add(new TFilter('active', '=', 'Y'), TExpression::OR_OPERATOR);
        $criteria->add(new TFilter('active', 'IS', NULL), TExpression::OR_OPERATOR);

        $assigned = new TDBCombo('assigned_id', 'permission', 'SystemUser', 'id', 'name', 'name', $criteria);
        $watcher = new TDBMultiSearch('watchers', 'permission', 'SystemUser', 'id', 'name', 'name', $criteria);
        $user = new TDBCombo('user_id', 'permission', 'SystemUser', 'id', 'name', 'name');

        $created_at = new TDateTime('created_at');
        $finished_at = new TDateTime('finished_at');

        $id->setEditable(FALSE);
        $id->setSize('30%');

        $files->enableFileHandling();
        $files->enablePopover();

        $description->setSize('100%', 250);
        $solution->setSize('100%', 250);

        $project->setSize('100%');
        $project->setEditable(false);

        $priority->setSize('100%');
        $category->setSize('100%');
        $title->setSize('100%');
        $status->setSize('100%');

        $assigned->setSize('100%');
        $assigned->enableSearch();

        $watcher->setSize('100%');
        $watcher->setMinLength(1);

        $user->setSize('100%');
        $user->setEditable(false);

        $related_issue->setSize('100%');
        $related_issue->setMinLength(1);
        $related_issue->setMask('[{id}] {title}');

        $created_at->setSize('100%');
        $created_at->setMask('dd/mm/yyyy hh:ii:ss');
        $created_at->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $created_at->setEditable(false);

        $finished_at->setSize('100%');
        $finished_at->setMask('dd/mm/yyyy hh:ii:ss');
        $finished_at->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $finished_at->setEditable(false);

        // add the form fields
        $this->form->appendPage('Geral');
        $this->form->addField($description);
        $this->form->addField($solution);
        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Projeto')], [$project]);
        $this->form->addFields([new TLabel('Aberto em')], [$created_at], [new TLabel('Finalizado em')], [$finished_at]);
        $this->form->addFields([new TLabel('Aberto por')], [$user], [new TLabel('Status')], [$status]);
        $this->form->addFields([new TLabel('Prioridade')], [$priority], [new TLabel('Categoria')], [$category]);
        $this->form->addFields([new TLabel('Atribuído a')], [$assigned], [new TLabel('Observadores')], [$watcher]);
        $this->form->addFields([new TLabel('Chamado relacionado')], [$related_issue]);
        $this->form->addFields([new TLabel('Título')], [$title]);

        $notebook = new BootstrapNotebookWrapper(new TNotebook);
        $notebook->appendPage('Descrição', $description);
        $notebook->appendPage('Solução', $solution);

        $this->form->addContent([$notebook]);
        $this->form->appendPage('Arquivos');
        $this->form->addFields([new TLabel('Arquivos')], [$files]);

        $project->addValidation('project_id', new TRequiredValidator);
        $priority->addValidation('priority_id', new TRequiredValidator);
        $category->addValidation('category_id', new TRequiredValidator);
        $title->addValidation('title', new TRequiredValidator);
        $description->addValidation('description', new TRequiredValidator);

        $action_project = new TAction([$this, 'onChangeProject']);
        $project->setChangeAction($action_project);

        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addHeaderAction('Comentar', new TAction(array($this, 'onClear')), 'fa:comment-alt');
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addActionLink(_t('List'), new TAction(array('IssueDataGridView', 'onReload')), 'fa:table blue');
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }


    public function onSave()
    {
        try {
            TTransaction::open($this->database);

            $data = $this->form->getData();

            $object = new Issue;
            $object->fromArray((array) $data);

            $this->form->validate();

            $status = new Status($object->status_id);
            if ($status->final_status && empty($object->finished_at)) {
                $object->finished_at = date('Y-m-d H:i:s');
            }

            $object->store();
            $object->clearParts();

            if (!empty($data->watchers)) {
                foreach ($data->watchers as $watcher_id) {
                    $object->addWatcher(new SystemUser($watcher_id));
                }
            }

            $this->saveFiles($object, $data, 'files', 'files/tickets/issues', 'IssueFile', 'file_path', 'issue_id');

            $this->form->setData($data);

            TTransaction::close();

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $this->afterSaveAction);

            return $object;
        } catch (Exception $e) // in case of exception
        {
            $object = $this->form->getData();

            $this->form->setData($object);

            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }


    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open($this->database);

                $class = $this->activeRecord;

                $object = new $class($key);

                $filesObject = $object->getFiles();
                if ($filesObject) {
                    $files = [];
                    foreach ($filesObject as $file) {
                        $files[] = ['idFile' => $file->id, 'fileName' => $file->file_path];
                    }
                    $object->files = $files;
                }

                $watchers = [];
                if ($watchers_db = $object->getWatchers()) {
                    foreach ($watchers_db as $watcher) {
                        $watchers[] = $watcher->id;
                    }
                }

                $object->watchers = $watchers;

                $this->form->setData($object);

                self::onChangeProject($object->toArray());

                TTransaction::close();

                return $object;
            } else {
                $this->form->clear(true);
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onChangeProject($param)
    {
        TTransaction::open('ticket');
        $usersProject = (new Project($param['project_id']))->getMembers();

        if ($usersProject) {
            $users = [];
            foreach ($usersProject as $user) {
                if (empty($user->active) || $user->active === 'Y') {
                    $users[$user->id] = $user->name;
                }
            }

            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', 'IN', array_keys($users)));
            $criteria->add(new TFilter('active', '=', 'Y'));
            $criteria->add(new TFilter('active', 'IS', NULL), TExpression::OR_OPERATOR);

            TDBCombo::reloadFromModel('form_Issue', 'assigned_id', 'permission', 'SystemUser', 'id', 'name', 'name', $criteria);
            $obj = new stdClass;
            $obj->assigned_id = $param['assigned_id'] ?? '';
            TForm::sendData('form_Issue', $obj);
        }

        TTransaction::close();
    }
}
