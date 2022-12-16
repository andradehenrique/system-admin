<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Container\TVBox;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TColor;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Wrapper\BootstrapFormBuilder;

class StatusFormView extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('ticket');    // defines the database
        $this->setActiveRecord('Status');   // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Status');
        $this->form->setFormTitle('Status');

        // create the form fields
        $id = new TEntry('id');
        $description = new TEntry('description');
        $color = new TColor('color');
        $order = new TEntry('order_number');
        $final_status = new TRadioGroup('final_status');

        $id->setEditable(FALSE);
        $id->setSize('30%');
        $color->setSize('50%');
        $order->setSize('35%');
        $order->setMask('9!');
        $final_status->setUseButton();
        $final_status->setLayout('horizontal');

        $final_status->addItems([1 => 'Sim', 0 => 'Não']);
        $final_status->setValue(0);

        // add the form fields
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);
        $this->form->addFields([new TLabel('Cor')], [$color]);
        $this->form->addFields([new TLabel('Ordem')], [$order]);
        $this->form->addFields([new TLabel('Estado final')], [$final_status]);

        $description->addValidation('description', new TRequiredValidator);

        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addActionLink(_t('List'), new TAction(array('StatusDataGridView', 'onReload')), 'fa:table blue');
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
