<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('brands', BrandController::class);
    $router->resource('replies', ReplyController::class);
    $router->resource('steps', StepController::class);
    $router->resource('trainings', TrainingController::class);
    $router->resource('integrations', IntegrationController::class);
    $router->resource('vidpops', VidpopController::class);
    $router->resource('soundtracks', SoundtrackController::class);
    $router->resource('memberships', MembershipController::class);
    $router->resource('pages', PageController::class);
    $router->resource('ai-videos', AiVideoController::class);
    $router->resource('plans', PlanController::class);
    $router->resource('coupons', CouponController::class);
    $router->resource('integration-videos', IntegrationVideoController::class);
});
