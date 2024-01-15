<?php

namespace App\Admin\Controllers;

use App\Models\Integration;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class IntegrationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Integration';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Integration());

        $grid->column('id', __('Id'));
        $grid->column('url', __('URL'));
        $grid->column('type', __('Type'))->display(function ($v) {
            if ($v === 1) {
                return 'Aweber';
            } else if ($v === 2) {
                return 'Getresponse';
            } else if ($v === 3) {
                return 'Mailchimp';
            } else if ($v === 4) {
                return 'CSV File';
            }
        });
        $grid->column('token', __('Token'));
        $grid->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
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
        $show = new Show(Integration::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('url', __('URL'));
        $show->field('type', __('Type'));
        $show->field('token', __('Token'));
        $show->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
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
        $form = new Form(new Integration());

        $form->text('url', __('URL'));
        $form->radio('type', __('Type'))->options([1 => 'Aweber', 2 => 'Getresponse', 3 => 'Mailchimp', 4 => 'CSV File']);
        $form->text('token', __('Token'));
        $form->select('user_id', __('Owner'))->options(User::all()->pluck('name', 'id'));
        return $form;
    }
}
