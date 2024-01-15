<?php

namespace App\Admin\Controllers;

use App\User;
use App\Models\AiVideo;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AiVideoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AiVideo';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AiVideo());

        $grid->column('id', __('Id'));
        $grid->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
        $grid->column('url', __('Url'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'));
        $grid->column('video_id', __('Video id'));
        $grid->column('status', __('Status'));
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
        $show = new Show(AiVideo::findOrFail($id));

        $show->field('id', __('Id'));
        $show->column('user', __('Owner'))->display(function ($v) {
            if ($v) {
                return $v['name'];
            }
            return null;
        });
        $show->field('url', __('Url'));
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('video_id', __('Video id'));
        $show->field('status', __('Status'));
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
        $form = new Form(new AiVideo());

        $form->url('url', __('Url'));
        $form->text('title', __('Title'));
        $form->text('thumbnail', __('Thumbnail'));
        $form->text('video_id', __('Video id'));
        $form->switch('status', __('Status'));
        $form->select('user_id', __('Owner'))->options(User::all()->pluck('name', 'id'));

        return $form;
    }
}
