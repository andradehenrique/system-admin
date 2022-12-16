<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use App\Helpers\HtmlHelper;

class StatusDataGridView extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('ticket');        // defines the database
        $this->setActiveRecord('Status');       // defines the active record
        $this->addFilterField('description', 'like', 'description'); // filter field, operator, form field
        $this->addFilterField('final_status', '=', 'final_status'); // filter field, operator, form field
        $this->setDefaultOrder('order_number', 'asc');  // define the default order

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Status');
        $this->form->setFormTitle('Status');

        $description = new TEntry('description');
        $final_status = new TRadioGroup('final_status');

        $final_status->setUseButton();
        $final_status->setLayout('horizontal');

        $final_status->addItems([1 => 'Sim', 0 => 'Não']);

        $this->form->addFields([new TLabel('Descrição:')], [$description]);
        $this->form->addFields([new TLabel('Estado final:')], [$final_status]);

        // add form actions
        $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink(_t('New'), new TAction(['StatusFormView', 'onClear']), 'fa:plus-circle green');
        $this->form->addActionLink(_t('Clear'), new TAction([$this, 'clear']), 'fa:eraser red');

        // keep the form filled with the search data
        $this->form->setData(TSession::getValue('StatusDataGridView_filter_data'));

        // creates the DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = "100%";

        // creates the datagrid columns
        $col_id    = new TDataGridColumn('id', 'Id', 'right', '10%');
        $col_description  = new TDataGridColumn('description', 'Descrição', 'left');
        $col_final_status  = new TDataGridColumn('final_status', 'Estado final', 'left');
        $col_order  = new TDataGridColumn('order_number', 'Ordem', 'left');

        $col_description->setTransformer([$this, 'setStatusColor']);
        $col_final_status->setTransformer(function ($value, $object) {
            if ($value) {
                return 'Sim';
            }
            return 'Não';
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_description);
        $this->datagrid->addColumn($col_final_status);
        $this->datagrid->addColumn($col_order);

        $col_id->setAction(new TAction([$this, 'onReload']),   ['order' => 'id']);
        $col_description->setAction(new TAction([$this, 'onReload']), ['order' => 'description']);
        $col_final_status->setAction(new TAction([$this, 'onReload']), ['order' => 'final_status']);
        $col_order->setAction(new TAction([$this, 'onReload']), ['order' => 'order_number']);

        $action1 = new TDataGridAction(['StatusFormView', 'onEdit'],   ['key' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'],   ['key' => '{id}']);

        $this->datagrid->addAction($action1, 'Edit',   'far:edit blue');
        $this->datagrid->addAction($action2, 'Delete', 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));

        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        // add the table inside the page
        parent::add($vbox);
    }

    /**
     * Clear filters
     */
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }

    public function setStatusColor($status, $object, $row)
    {
        return HtmlHelper::createLabelByColor($status, $object->color);
    }
}
