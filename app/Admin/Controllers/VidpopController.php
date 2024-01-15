<?php

namespace App\Admin\Controllers;

use App\Models\Vidpop;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\User;

class VidpopController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vidpop';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Vidpop());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
        $grid->column('end_bg', __('End bg'));
        $grid->column('end_color', __('End color'));
        $grid->column('end_position', __('End position'));
        $grid->column('title', __('End Text'));
        $grid->column('content', __('Content'));
        
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
        $show = new Show(Vidpop::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('end_font', __('End font'));
        $show->field('end_bg', __('End bg'));
        $show->field('end_color', __('End color'));
        $show->field('end_position', __('End position'));
        $show->field('title', __('End Text'));
        $show->field('content', __('Content'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Vidpop());

        $form->text('name', __('Name'));
        $form->select('user_id', __('Owner'))->options(User::all()->pluck('name', 'id'));
        $form->text('end_font', __('End font'));
        $form->text('end_bg', __('End bg'));
        $form->text('end_color', __('End color'));
        $form->text('end_position', __('End position'));
        $form->text('title', __('End Text'));
        $form->text('content', __('Content'));

        return $form;
    }
}
