<?php

namespace App\Admin\Controllers;

use App\Models\Membership;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

class MembershipController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Memberships')
            ->description('list')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Membership')
            ->description('detail')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Membership')
            ->description('edit')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Membership')
            ->description('create')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Membership());

        $grid->id('Id');
        $grid->label('Code');
        $grid->name('Name');
        $grid->jv_product_id('Jvzoo Product Id');
        $grid->cb_product_id('Clickbank Product Id');
        $grid->email_subject('Email Subject');
        $grid->credits('Initial Credits');
        $grid->updated_at('Updated at');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Membership::findOrFail($id));

        $show->id('Id');
        $show->label('Code');
        $show->name('Name');
        $show->jv_product_id('Jvzoo Product Id');
        $show->cb_product_id('Clickbank Product Id');
        $show->email_subject('Email Subject');
        $show->email_content('Email Content');
        $show->jv_callback('Jvzoo Callback Url');
        $show->cb_callback('Clickbank Callback Url');
        $show->credits('Initial Credits');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Membership());

        $form->text('label', 'Code');
        $form->text('name', 'Name');
        $form->text('jv_product_id', 'Jvzoo Product Id');
        $form->text('jv_callback', 'Jvzoo Callback');
        $form->text('cb_product_id', 'Clickbank Product Id');
        $form->text('cb_callback', 'Clickbank Callback');
        $form->decimal('credits', 'Initial Credits');
        $form->text('email_subject', 'Email Subject');
        $form->textarea('email_content', 'Email Content')->rows(15);

        return $form;
    }
}
