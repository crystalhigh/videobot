<?php

use Illuminate\Support\Facades\Route;
use App\Models\IntegrationVideo;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('upload-file', 'ReplyController@upload');
Route::post('upload-audio', 'ReplyController@uploadAudio');
Route::post('save-reply', 'ReplyController@save');
Route::get('live', 'LiveController@live');
Route::post('paddle-hook', 'PaymentController@paddleCB');
Route::post('impression', 'ImpressionController@load');
Route::get('appsumo-user/{id}', 'AppsumoController@loadUser');

Route::post('appsumo-profile', 'AuthController@appsumo');

Route::group(['middleware' => 'cors'], function () {
    Route::post('appsumo-token', 'AppsumoController@token');
});

Route::group(['middleware' => ['cors', 'auth:api']], function () {
    Route::post('appsumo-notify', 'AppsumoController@notify');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('logout', 'AuthController@logout');
    Route::post('forgot', 'ForgotPasswordController@forgot');
    Route::post('reset', 'ForgotPasswordController@reset');
    Route::post('redeem', 'AuthController@redeem')->name('redeem');
});

Route::group(['middleware' => 'auth:api'], function () {
    // campaign
    Route::get('brands', 'BrandController@load');
    Route::post('brand', 'BrandController@save');
    Route::delete('brand/{id}', 'BrandController@delete');
    Route::post('upload-logo', 'BrandController@uploadLogo');
    Route::get('trainings', 'TrainingController@load');
    Route::get('subusers', 'UserController@subusers');

    // vidpop
    // get all vidpop
    Route::get('vidpops', 'VidpopController@all');
    Route::get('vidpop-adv', 'VidpopController@advertise');

    Route::get('vidpop/{id}', 'VidpopController@load');
    Route::get('step-counts/{id}', 'VidpopController@stepCounts');
    Route::post('vidpop', 'VidpopController@save');
    Route::put('vidpop/{id}', 'VidpopController@update');
    Route::post('vidpop-assignee', 'VidpopController@saveAssignee');
    Route::delete('vidpop/{id}', 'VidpopController@delete');
    Route::get('clone-vidpop', 'VidpopController@cloneVidpop');

    // step
    Route::get('steps', 'StepController@load');
    Route::get('step/{id}', 'StepController@get');
    Route::get('first-step', 'StepController@firstStep');
    Route::get('publish-widget', 'StepController@publishWidget');
    Route::post('create-step', 'StepController@create');

    Route::post('update-step', 'StepController@save');
    Route::delete('step/{id}', 'StepController@delete');
    Route::post('update-step-index', 'StepController@updateIndex');
    Route::post('upload-sound', 'StepController@uploadSound');

    Route::get('vidpop-by-id/{id}', 'VidpopController@getVidpopByID');

    Route::post('save-end-screen', 'VidpopController@saveEndScreen');

    Route::get('uploaded-video', 'LibraryController@uploadedVideos');
    Route::get('uploaded-image', 'LibraryController@uploadedImages');
    Route::get('pixabay-video', 'LibraryController@pixabay');
    Route::get('pixels-video', 'LibraryController@pixels');
    Route::get('pixabay-image', 'LibraryController@pixabayImage');
    Route::get('giphy-video', 'LibraryController@giphy');

    Route::post('upload-image', 'LibraryController@uploadImage');

    Route::post('create-step-library', 'LibraryController@createStepLibrary');

    Route::get('replies', 'ReplyController@load');
    Route::get('reply-count', 'ReplyController@replyCounts');
    Route::delete('reply/{id}', 'ReplyController@delete');
    Route::delete('reply-group/{id}', 'ReplyController@deleteGroup');
    Route::get('csv-download', 'ReplyController@csvDownload');
    Route::get('read-reply/{id}', 'ReplyController@readReply');

    Route::get('templates', 'VidpopController@templates');
    Route::get('clone-template', 'VidpopController@clone');

    // stat
    Route::post('stat-impressions', 'StatController@statImpressions');
    Route::post('stat-replies', 'StatController@statReplies');
    Route::get('monthly-impressions', 'StatController@monthlyImpression');

    Route::get('languages', function () {
        return response()->json(config('tts'), 200);
    });

    // integration
    Route::post('integration', 'IntegrationController@save');
    Route::post('integration/unauthorize', 'IntegrationController@unauthorize');
    Route::get('integrations', 'IntegrationController@load');
    Route::get('integration-list', 'IntegrationController@list');

    // user profile
    Route::post('save-profile', 'UserController@save');
    Route::post('upload-avatar', 'UserController@uploadAvatar');
    Route::post('update-user', 'UserController@updateUser');
    Route::post('verify-credit', 'UserController@verifyCredit');
    Route::delete('delete-user/{id}', 'UserController@deleteUser');
    Route::get('cancel-subscribe', 'UserController@cancelSubscribe');
    Route::post('update-subscribe', 'UserController@updateSubscribe');
    Route::get('update-show-tutorial', 'UserController@updateTutorial');
    Route::get('advertiser-list', 'UserController@getAdvertisers');

    // ai
    Route::get('ai-avatars', 'AIController@avatars');

    // tts
    Route::post('ai-tts', 'AIController@tts');
    Route::post('ai-upload-audio', 'AIController@uploadAudio');
    Route::post('ai-generate-video', 'AIController@generateVideo');
    Route::post('createStep-ai', 'AIController@createStep');
    Route::get('ai-videos', 'AIController@load');
    Route::delete('ai-delete/{id}', 'AIController@delete');

    Route::get('page', 'PageController@load');

    Route::get('soundtracks', 'SoundtrackController@load');

    Route::get('plans', 'PlanController@load');
    Route::get('plan-info', 'PlanController@info');

    // coupons
    Route::get('coupons', 'CouponController@load');
    Route::delete('coupon/{id}', 'CouponController@delete');
    Route::post('coupons', 'CouponController@create');
    Route::put('coupon/{id}', 'CouponController@update');

    Route::get('storage-status', 'UserController@storageStatus');

    Route::get('integration-videos', function () {
        return IntegrationVideo::all();
    });
    Route::get('metrics', 'VidpopMetricsController@get');
    Route::post('metrics', 'VidpopMetricsController@create');

    Route::get('advertiser-request', 'PublisherVidpopupController@advertiserRequest');
    Route::get('publisher-response', 'PublisherVidpopupController@publisherResponse');
    Route::put('publisher-vidpopup/{id}', 'PublisherVidpopupController@update');
    Route::get('adv-pub-count', 'PublisherVidpopupController@reqResCount');

    Route::post('publisher-ban', 'PublisherBanController@create');
    Route::delete('publisher-ban/{id}', 'PublisherBanController@delete');

    Route::get('transaction', 'PublisherVidpopupController@transaction');

    Route::get('payout', 'PublisherPayoutController@payout');
    Route::post('payout', 'PublisherPayoutController@create');
    Route::get('payout-distribution', 'PublisherPayoutController@getPayoutDistribution');

    Route::get('admin-payout', 'PublisherPayoutController@adminPayout');
    Route::post('admin-advertiser-topup', 'PublisherPayoutController@adminAdvertiserTopUp');
    Route::post('admin-publisher-withdraw', 'PublisherPayoutController@adminPublisherWithdraw');
    Route::get('admin-pay-publisher', 'PublisherPayoutController@getAdminPayPublisher');
    Route::post('admin-pay-publisher', 'PublisherPayoutController@updateAdminPayPublisher');

});
Route::put('metrics/{id}', 'VidpopMetricsController@update');
Route::get('metrics-click-count', 'VidpopMetricsController@getClickCount');
Route::get('conversion-data', 'VoluumController@getConversionData');
Route::get('published-status', 'PublisherVidpopupController@getPublishedStatus');

// Integrations Test
Route::post(
    'integrations/{autoresponder}/test',
    'IntegrationTestController@test'
);
