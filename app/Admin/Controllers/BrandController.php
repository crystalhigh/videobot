<?php

namespace App\Admin\Controllers;

use App\User;
use App\Models\Brand;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BrandController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Brand';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Brand());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('logo', __('Logo'));
        $grid->column('url', __('Url'));
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
        $show = new Show(Brand::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('logo', __('Logo'));
        $show->field('url', __('Url'));
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
        $form = new Form(new Brand());

        $form->text('name', __('Name'))->rules('required');
        $form->cropper('logo', __('Logo'))->cRatio(100, 100)->move('uploads/brand_logo');
        $form->url('url', __('Url'));
        $form->select('user_id', __('Owner'))->options(User::all()->pluck('name', 'id'));

        return $form;
    }
}
