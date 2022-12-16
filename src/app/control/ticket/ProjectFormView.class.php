<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Validator\TRequiredValidator;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Wrapper\TDBMultiSearch;

class ProjectFormView extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('ticket');    // defines the database
        $this->setActiveRecord('Project');   // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Project');
        $this->form->setFormTitle('Projeto');

        // create the form fields
        $id = new TEntry('id');
        $description = new TEntry('description');
        $members = new TDBMultiSearch('members', 'ticket', 'SystemUser', 'id', 'name');
        $members->setMinLength(1);
        $id->setEditable(FALSE);
        $id->setSize('30%');

        // add the form fields
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);
        $this->form->addFields([new TLabel('Membros')], [$members]);

        $description->addValidation('description', new TRequiredValidator);

        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addActionLink(_t('List'), new TAction(array('ProjectDataGridView', 'onReload')), 'fa:table blue');
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try {
            if (empty($this->database)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }

            if (empty($this->activeRecord)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }

            // open a transaction with database
            TTransaction::open($this->database);

            // get the form data
            $object = $this->form->getData($this->activeRecord);

            // validate data
            $this->form->validate();

            // stores the object
            $object->store();
            $object->clearParts();

            if (!empty($object->members)) {
                foreach ($object->members as $member_id) {
                    $object->addMember(new SystemUser($member_id));
                }
            }

            // fill the form with the active record data
            $this->form->setData($object);

            // close the transaction
            TTransaction::close();

            // shows the success message
            if (isset($this->useMessages) and $this->useMessages === false) {
                AdiantiCoreApplication::loadPageURL($this->afterSaveAction->serialize());
            } else {
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $this->afterSaveAction);
            }

            return $object;
        } catch (Exception $e) // in case of exception
        {
            // get the form data
            $object = $this->form->getData();

            $this->form->setData($object);

            // shows the exception error message
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (empty($this->database)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }

            if (empty($this->activeRecord)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }

            if (isset($param['key'])) {
                // get the parameter $key
                $key = $param['key'];

                // open a transaction with database
                TTransaction::open($this->database);

                $class = $this->activeRecord;

                // instantiates object
                $object = new $class($key);

                $members = [];
                if ($members_db = $object->getMembers()) {
                    foreach ($members_db as $member) {
                        $members[] = $member->id;
                    }
                }

                $object->members = $members;

                // fill the form with the active record data
                $this->form->setData($object);

                // close the transaction
                TTransaction::close();

                return $object;
            } else {
                $this->form->clear(true);
            }
        } catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
