<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\User;

class CouponController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Coupon';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coupon());

        $grid->column('id', __('Id'));
        $grid->column('coupon', __('Coupon'));
        $grid->column('user', __('User'))->display(function($v) {
            if ($v) {
                return $v['email'];
            }
            return null;
        });
        $grid->column('once', __('Type'))->display(function ($v) {
            if ($v) {
                return 'Once';
            }
            return 'Monthly';
        });
        $grid->column('level', __('Level'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->filter(function ($filter) {
            $filter->like('coupon', 'Coupon');
        });

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
        $show = new Show(Coupon::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('coupon', __('Coupon'));
        $show->field('user_id', __('User Id'));
        $show->field('once', __('Once'));
        $show->field('level', __('Level'));
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
        $form = new Form(new Coupon());

        $form->text('coupon', __('Coupon'));
        $form->select('user_id', __('Owner'))->options(User::all()->pluck('name', 'id'));
        $form->switch('once', __('Once'))->default(1);
        $form->text('level', __('Level'))->default('FE');

        return $form;
    }
}
