<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TMultiFile;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Wrapper\BootstrapFormBuilder;
use App\ValueObjects\Ticket\Status;

class NewIssueForm extends TPage
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
        $project = new TCombo('project_id');
        $priority = new TDBCombo('priority_id', $this->database, 'Priority', 'id', 'description');
        $category = new TDBCombo('category_id', $this->database, 'Category', 'id', 'description');
        $title = new TEntry('title');
        $files = new TMultiFile('files');
        $description = new THtmlEditor('description');
        $id->setEditable(FALSE);
        $id->setSize('30%');
        $files->enableFileHandling();
        $files->enablePopover();
        $description->setSize('100%', 250);


        TTransaction::open('permission');
        $logged = SystemUser::newFromLogin(TSession::getValue('login'));
        TTransaction::close();

        TTransaction::open($this->database);
        $projects = Project::getUserProjects($logged);
        $project_ids = array();
        foreach ($projects as $projectModel) {
            $project_ids[$projectModel->id] = $projectModel->description;
        }
        TTransaction::close();

        $project->addItems($project_ids);

        // if just one project, its the default
        if (count($projects) === 1) {
            $project_keys = array_keys($project_ids);
            $project->setValue($project_keys[0]);
        }

        // add the form fields
        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Projeto')], [$project]);
        $this->form->addFields([new TLabel('Prioridade')], [$priority], [new TLabel('Categoria')], [$category]);
        $this->form->addFields([new TLabel('Título')], [$title]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);
        $this->form->addFields([new TLabel('Arquivos')], [$files]);

        $project->addValidation('project_id', new TRequiredValidator);
        $priority->addValidation('priority_id', new TRequiredValidator);
        $category->addValidation('category_id', new TRequiredValidator);
        $title->addValidation('title', new TRequiredValidator);
        $description->addValidation('description', new TRequiredValidator);

        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
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

            TTransaction::open('permission');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            TTransaction::close();

            TTransaction::open($this->database);

            $data = $this->form->getData();

            $object = new Issue;
            $object->fromArray((array) $data);

            if (!empty($object->status_id) && (int) $object->status_id !== Status::NEW) {
                throw new Exception("Não foi possível editar o chamado, pois o mesmo encontra-se iniciado ou finalizado.");
            }

            $this->form->validate();

            $object->user_id = $logged->id;
            $object->status_id = Status::NEW;

            $object->store();

            $this->saveFiles($object, $data, 'files', 'files/tickets/issues', 'IssueFile', 'file_path', 'issue_id');

            $data->id = $object->id;
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

                if (!empty($object->status_id) && (int) $object->status_id !== Status::NEW) {
                    throw new Exception("Não foi possível editar o chamado, pois o mesmo encontra-se iniciado ou finalizado.");
                }

                $filesObject = $object->getFiles();
                if ($filesObject) {
                    $files = [];
                    foreach ($filesObject as $file) {
                        $files[] = ['idFile' => $file->id, 'fileName' => $file->file_path];
                    }
                    $object->files = $files;
                }

                $this->form->setData($object);

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
}
