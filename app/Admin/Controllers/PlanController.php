<?php

namespace App\Admin\Controllers;

use App\Models\Plan;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PlanController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Plan';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Plan());

        $grid->column('id', __('Id'));
        $grid->column('product', __('Product'));
        $grid->column('title', __('Title'));
        $grid->column('subtitle', __('Subtitle'));
        $grid->column('index', __('Index'));
        $grid->column('level', __('Level'));
        $grid->column('type', __('Type'));
        $grid->column('storage', __('Storage'));
        $grid->column('whitelabels', __('WhiteLabels'));
        $grid->column('features', __('Features'));

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
        $show = new Show(Plan::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product', __('Product'));
        $show->field('title', __('Title'));
        $show->field('subtitle', __('Subtitle'));
        $show->field('index', __('Index'));
        $show->field('level', __('Level'));
        $show->field('type', __('Type'));
        $show->field('storage', __('Storage'));
        $show->field('whitelabels', __('WhiteLabels'));
        $show->field('email_content', __('Email Content'));
        $show->field('features', __('Features'));
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
        $form = new Form(new Plan());

        $form->text('product', __('Product'));
        $form->text('title', __('Title'));
        $form->text('subtitle', __('Subtitle'));
        $form->number('index', __('Index'));
        $form->text('level', __('Level'));
        $form->radio('type', __('Type'))->options(['main' => 'Main', 'appsumo' => 'AppSumo']);
        $form->number('storage', __('Storage'));
        $form->number('whitelabels', __('WhiteLabels'));
        $form->summernote('email_content', __('Email Content'));
        $form->textarea('features', __('Features'));

        return $form;
    }
}
