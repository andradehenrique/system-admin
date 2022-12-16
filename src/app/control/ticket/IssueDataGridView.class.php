<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Database\TCriteria;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use App\Helpers\DateHelper;
use App\Helpers\HtmlHelper;
use App\ValueObjects\Ticket\Status;

class IssueDataGridView extends TPage
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
        $this->setActiveRecord('Issue');       // defines the active record
        $this->addFilterField('status_id', '=', 'status_id'); // filter field, operator, form field
        $this->addFilterField('project_id', '=', 'project_id'); // filter field, operator, form field
        $this->addFilterField('priority_id', '=', 'priority_id'); // filter field, operator, form field
        $this->addFilterField('category_id', '=', 'category_id'); // filter field, operator, form field
        $this->addFilterField('title', 'like', 'title'); // filter field, operator, form field
        $this->setDefaultOrder('updated_at', 'desc');  // define the default order

        TTransaction::open('permission');
        $user = SystemUser::newFromLogin(TSession::getValue('login'));
        $is_admin    = $user->checkInGroup(new SystemGroup(1));
        $is_manager  = $user->checkInGroup(new SystemGroup(3));
        $is_member   = $user->checkInGroup(new SystemGroup(4));
        $is_customer = $user->checkInGroup(new SystemGroup(5));
        TTransaction::close();

        if ($is_admin || $is_manager || $is_member) {
            $this->addFilterField('user_id', '=', 'user_id'); // filter field, operator, form field
            $this->addFilterField('assigned_id', '=', 'assigned_id'); // filter field, operator, form field
            $this->addFilterField('created_at', '>=', 'created_at_start'); // filter field, operator, form field
            $this->addFilterField('created_at', '<=', 'created_at_end'); // filter field, operator, form field
            $this->addFilterField('updated_at', '>=', 'updated_at_start'); // filter field, operator, form field
            $this->addFilterField('updated_at', '<=', 'updated_at_end'); // filter field, operator, form field
            $this->addFilterField('finished_at', '>=', 'finished_at_start'); // filter field, operator, form field
            $this->addFilterField('finished_at', '<=', 'finished_at_end'); // filter field, operator, form field
        }

        if ($is_customer) {
            $this->setCriteria(TCriteria::create(['user_id' => $user->id]));
        }

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Issue');
        $this->form->setFormTitle('Chamados');

        // create the form fields
        $filter_status = new TDBCombo('status_id', $this->database, 'Status', 'id', 'description', 'order_number');
        $filter_project = new TDBCombo('project_id', $this->database, 'Project', 'id', 'description');
        $filter_priority = new TDBCombo('priority_id', $this->database, 'Priority', 'id', 'description');
        $filter_category = new TDBCombo('category_id', $this->database, 'Category', 'id', 'description');
        $filter_user = new TDBCombo('user_id', 'permission', 'SystemUser', 'id', 'name', 'name');
        $filter_title = new TEntry('title');
        $filter_created_date_start = new TDate('created_at_start');
        $filter_created_date_end = new TDate('created_at_end');
        $filter_updated_date_start = new TDate('updated_at_start');
        $filter_updated_date_end = new TDate('updated_at_end');
        $filter_finished_date_start = new TDate('finished_at_start');
        $filter_finished_date_end = new TDate('finished_at_end');
        $filter_assigned = new TDBCombo('assigned_id', 'permission', 'SystemUser', 'id', 'name', 'name');

        $filter_status->setSize('100%');
        $filter_project->setSize('100%');
        $filter_priority->setSize('100%');
        $filter_category->setSize('100%');
        $filter_title->setSize('100%');

        $filter_created_date_start->setSize('100%');
        $filter_created_date_start->setMask('dd/mm/yyyy');
        $filter_created_date_start->setDatabaseMask('yyyy-mm-dd');

        $filter_created_date_end->setSize('100%');
        $filter_created_date_end->setMask('dd/mm/yyyy');
        $filter_created_date_end->setDatabaseMask('yyyy-mm-dd');

        $filter_updated_date_start->setSize('100%');
        $filter_updated_date_start->setMask('dd/mm/yyyy');
        $filter_updated_date_start->setDatabaseMask('yyyy-mm-dd');

        $filter_updated_date_end->setSize('100%');
        $filter_updated_date_end->setMask('dd/mm/yyyy');
        $filter_updated_date_end->setDatabaseMask('yyyy-mm-dd');

        $filter_finished_date_start->setSize('100%');
        $filter_finished_date_start->setMask('dd/mm/yyyy');
        $filter_finished_date_start->setDatabaseMask('yyyy-mm-dd');

        $filter_finished_date_end->setSize('100%');
        $filter_finished_date_end->setMask('dd/mm/yyyy');
        $filter_finished_date_end->setDatabaseMask('yyyy-mm-dd');

        $filter_user->enableSearch();
        $filter_user->setSize('100%');

        $filter_assigned->enableSearch();
        $filter_assigned->setSize('100%');

        $this->form->addFields([new TLabel('Status')], [$filter_status], [new TLabel('Projeto')], [$filter_project]);
        $this->form->addFields([new TLabel('Prioridade')], [$filter_priority], [new TLabel('Categoria')], [$filter_category]);

        if ($is_admin || $is_manager || $is_member) {
            $this->form->addFields([new TLabel('Atribuído a')], [$filter_assigned], [new TLabel('Usuário')], [$filter_user]);
            $this->form->addFields([new TLabel('Criado em')], [$filter_created_date_start], [new TLabel('Até')], [$filter_created_date_end]);
            $this->form->addFields([new TLabel('Atualizado em')], [$filter_updated_date_start], [new TLabel('Até')], [$filter_updated_date_end]);
            $this->form->addFields([new TLabel('Finalizado em')], [$filter_finished_date_start], [new TLabel('Até')], [$filter_finished_date_end]);
            $this->form->addExpandButton();
        }

        $this->form->addFields([new TLabel('Título')], [$filter_title]);

        // add form actions
        $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink(_t('New'), new TAction(['NewIssueForm', 'onClear']), 'fa:plus-circle green');
        $this->form->addActionLink(_t('Clear'), new TAction([$this, 'clear']), 'fa:eraser red');

        // keep the form filled with the search data
        $this->form->setData(TSession::getValue('IssueDataGridView_filter_data'));

        // creates the DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = "100%";
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $col_id = new TDataGridColumn('id', 'Id', 'right', 40);
        $col_title = new TDataGridColumn('title', 'Título', 'left');
        $col_status = new TDataGridColumn('status->description', 'Status', 'left');
        $col_priority = new TDataGridColumn('priority->description', 'Prioridade', 'left');
        $col_category = new TDataGridColumn('category->description', 'Categoria', 'left');
        $col_project = new TDataGridColumn('project->description', 'Projeto', 'left');
        $col_assigned = new TDataGridColumn('assigned->name', 'Atribuído a', 'left');
        $col_created_at = new TDataGridColumn('created_at', 'Aberto em', 'left');
        $col_user = new TDataGridColumn('user->name', 'Aberto por', 'left');

        $col_status->setTransformer([$this, 'setStatusColor']);
        $col_priority->setTransformer([$this, 'setPriorityColor']);
        $col_category->setTransformer([$this, 'setCategoryColor']);
        $col_created_at->setTransformer([$this, 'formatDate']);

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_title);
        $this->datagrid->addColumn($col_status);
        $this->datagrid->addColumn($col_priority);
        $this->datagrid->addColumn($col_category);
        $this->datagrid->addColumn($col_project);
        $this->datagrid->addColumn($col_assigned);
        $this->datagrid->addColumn($col_created_at);
        $this->datagrid->addColumn($col_user);

        $col_id->setAction(new TAction([$this, 'onReload']),   ['order' => 'id']);
        $col_title->setAction(new TAction([$this, 'onReload']), ['order' => 'description']);
        $col_status->setAction(new TAction([$this, 'onReload']), ['order' => 'status_id']);
        $col_priority->setAction(new TAction([$this, 'onReload']), ['order' => 'priority_id']);
        $col_category->setAction(new TAction([$this, 'onReload']), ['order' => 'category_id']);
        $col_project->setAction(new TAction([$this, 'onReload']), ['order' => 'project_id']);
        $col_assigned->setAction(new TAction([$this, 'onReload']), ['order' => 'assigned_id']);
        $col_created_at->setAction(new TAction([$this, 'onReload']), ['order' => 'created_at']);
        $col_user->setAction(new TAction([$this, 'onReload']), ['order' => 'user_id']);

        if ($is_admin || $is_manager || $is_member) {
            $action1 = new TDataGridAction(['UpdateIssueForm', 'onEdit'],   ['key' => '{id}']);
            $action2 = new TDataGridAction([$this, 'onDelete'],   ['key' => '{id}']);

            $this->datagrid->addAction($action1, 'Edit',   'far:edit blue');
            $this->datagrid->addAction($action2, 'Delete', 'far:trash-alt red');
        } else if ($is_customer) {
            $action1 = new TDataGridAction(['NewIssueForm', 'onEdit'],   ['key' => '{id}']);
            $action2 = new TDataGridAction([$this, 'onDelete'],   ['key' => '{id}']);
            $action1->setDisplayCondition([$this, 'issueIsNew']);
            $action2->setDisplayCondition([$this, 'issueIsNew']);

            $this->datagrid->addAction($action1, 'Edit',   'far:edit blue');
            $this->datagrid->addAction($action2, 'Delete', 'far:trash-alt red');
        }

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
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
    public function clear(): void
    {
        $this->clearFilters();
        $this->onReload();
    }

    public function issueIsNew($object): bool
    {
        return (int) $object->status_id === Status::NEW;
    }

    public function setStatusColor($status, $object, $row)
    {
        return HtmlHelper::createLabelByColor($status, $object->status->color);
    }

    public function setPriorityColor($priority, $object, $row)
    {
        return HtmlHelper::createLabelByColor($priority, $object->priority->color);
    }

    public function setCategoryColor($category, $object, $row)
    {
        return HtmlHelper::createLabelByColor($category, $object->category->color);
    }

    public function formatDate($date)
    {
        return DateHelper::formatDate($date);
    }
}
