<?php

namespace App\Admin\Controllers;

use App\Models\Reply;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReplyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reply';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Reply());

        $grid->column('id', __('Id'));
        $grid->column('vidpop_id', __('Vidpop id'));
        $grid->column('step_id', __('Step id'));
        $grid->column('type', __('Type'));
        $grid->column('data', __('Data'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Reply::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('vidpop_id', __('Vidpop id'));
        $show->field('step_id', __('Step id'));
        $show->field('type', __('Type'));
        $show->field('data', __('Data'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Reply());

        $form->number('vidpop_id', __('Vidpop id'));
        $form->number('step_id', __('Vidpop id'));
        $form->text('type', __('Type'));
        $form->textarea('data', __('Data'));

        return $form;
    }
}
