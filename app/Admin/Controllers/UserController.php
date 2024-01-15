<?php

namespace App\Admin\Controllers;

use App\User;
use App\Models\Impression;
use App\Models\Reply;
use App\Models\Vidpop;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';
    protected $id = 1;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        // $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('originator', __('Originator'))->display(function ($v) {
            $user = User::find($v);
            if ($user) {
                return $user->name;
            }
            return '';
        });
        $grid->column('level', __('Level'));
        $grid->column('role', __('Role'));
        $grid->column('balance', __('Balance'));
        $grid->column('approved', __('Approved'));
        $grid->column('s3', __('Storage usage'));
        $grid->column('video_play', __('Video Plays'))->display(function () {
            $count = Impression::where('user_id', $this->id)->count();
            return $count;
        });
        $grid->column('replies', __('Replies'))->display(function () {
            $replies = Reply::where('user_id', $this->id)->count();
            return $replies;
        });
        $grid->column('vidpopup', __('Vidpopup'))->display(function () {
            $res = Vidpop::where('user_id', $this->id)->count();
            return $res;
        });
        // $grid->column('avatar', __('Avatar'));
        $grid->column('pay_type', __('Pay type'));
        $grid->column('credit', __('Credit'));
        $grid->column('whitelabels', __('Subusers'));
        $grid->column('template_admin', __('Template Admin'));
        // $grid->column('created_at', __('Created at'));
        $grid->filter(function ($filter) {
            $filter->like('email', 'Email');
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('originator', __('Originator'));
        $show->field('level', __('Level'));
        $show->field('role', __('Role'));
        $grid->field('balance', __('Balance'));
        $grid->field('approved', __('Approved'));
        $show->field('s3', __('Storage usage'));
        $show->field('avatar', __('Avatar'));
        $show->field('pay_type', __('Pay type'));
        $show->field('credit', __('Credit'));
        $show->field('country', __('Country'));
        $show->field('city', __('City'));
        $show->field('state', __('State'));
        $show->field('zip_code', __('Zip code'));
        $show->field('whitelabels', __('Subusers'));
        $show->field('integration', __('Integration'));
        $show->field('integration_data', __('Integration data'));
        $show->field('remember_token', __('Remember token'));
        $show->field('template_admin', __('Template Admin'));
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
        $form = new Form(new User());

        $form->text('name', __('Name'))->rules('required');
        $form->email('email', __('Email'))->rules('email|required');
        $form->password('password', __('Password'))->creationRules('required|min:8');
        $form->number('originator', __('Originator'))->default(0);
        // $form->text('level', __('Level'))->default('FE');
        $form->radio('role', 'Role')->options(['origin' => 'Origin', 'advertiser' => 'Advertiser', 'publisher' => 'Publisher'])->default('origin');
        $form->select('level', 'Level')->options(['FET' => 'FET', 'FE' => 'FE', 'OTO1' => 'OTO1', 'OTO2' => 'OTO2', 'TIER1' => 'AppSumo Tier1', 'TIER2' => 'AppSumo Tier2', 'TIER3' => 'AppSumo Tier3'])->rules('required')->default('FET');
        $form->decimal('balance', __('Balance'))->default(0.00);
        $form->radio('approved', __('Approved'))->options([1 => 'Yes', 0 => 'No']);
        $form->image('avatar', __('Avatar'));
        $form->number('pay_type', __('Pay type'))->default(0);
        $form->decimal('credit', __('Credit'))->default(0.00);
        $form->text('country', __('Country'));
        $form->text('city', __('City'));
        $form->text('state', __('State'));
        $form->text('zip_code', __('Zip code'));
        $form->radio('template_admin', __('Template Admin'))->options([1 => 'Yes', 0 => 'No']);
        $form->number('whitelabels', __('Sub users'))->default(0);

        $form->radio('show_tutorial', __('Show Tutorial'))->options([1 => 'Yes', 0 => 'No']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            } else {
                $form->password = $form->model()->password;
            }
        });

        return $form;
    }
}
