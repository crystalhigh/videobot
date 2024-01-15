<?php

namespace App\Admin\Controllers;

use App\Models\IntegrationVideo;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class IntegrationVideoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'IntegrationVideo';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new IntegrationVideo());

        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'));
        $grid->column('url', __('Url'));
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
        $show = new Show(IntegrationVideo::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('url', __('Url'));
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
        $form = new Form(new IntegrationVideo());

        $form->radio('type', __('Type'))->options(['aweber' => 'Aweber', 'getresponse' => 'Getresponse', 'mailchimp' => 'Mailchimp', 'zapier' => 'Zapier', 'pabbly' => 'Pabbly', 'sendinblue' => 'SendInBlue', 'csv' => 'CSV']);
        $form->url('url', __('Url'));

        return $form;
    }
}
