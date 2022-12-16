<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Container\TVBox;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TColor;
use Adianti\Wrapper\BootstrapFormBuilder;

class TagFormView extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('ticket');    // defines the database
        $this->setActiveRecord('Tag');   // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Tag');
        $this->form->setFormTitle('Tag');

        // create the form fields
        $id = new TEntry('id');
        $description = new TEntry('description');
        $color = new TColor('color');
        $id->setEditable(FALSE);
        $id->setSize('30%');
        $color->setSize('50%');

        // add the form fields
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);
        $this->form->addFields([new TLabel('Cor')], [$color]);

        $description->addValidation('description', new TRequiredValidator);

        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addActionLink(_t('List'), new TAction(array('TagDataGridView', 'onReload')), 'fa:table blue');
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
