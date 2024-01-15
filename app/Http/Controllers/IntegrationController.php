<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\Vidpop;
use App\Services\AutoresponderService;
use App\Services\AutoresponderServiceException;
use App\Utils\Autoresponder\Autoresponder;
use App\Utils\CommonUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IntegrationController extends Controller
{
    /**
     * @var \App\Services\AutoresponderService
     */
    private $autoresponderService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AutoresponderService  $autoresponderService
     * @return void
     */
    public function __construct(AutoresponderService $autoresponderService)
    {
        $this->autoresponderService = $autoresponderService;
    }

    public function load()
    {
        try {
            $userid = CommonUtils::getUserId();
            $integrations = Integration::where(
                'user_id',
                $userid
            )->get();
            return response()->json($integrations, 200);
        } catch (\Throwable $e) {
            Log::error('Integration error : ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 400);
        }
    }

    /**
     * Integrations authentication.
     *
     * @param  string  $autoresponder The autoresponder name
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function oauth($autoresponder)
    {
        $autoresponderInstance = Autoresponder::factory($autoresponder);

        if (!$autoresponderInstance->withOAuth()) {
            return response(
                'This autoresponder does not support OAuth 2.0',
                400
            );
        }

        if (!request()->has('code')) {
            return redirect($autoresponderInstance->getAuthorizationUrl());
        }

        try {
            $accessToken = $autoresponderInstance->getAccessToken(
                request('code')
            );
        } catch (\Throwable $e) {
            Log::error('Integration oauth: ' . $e->getMessage());

            throw new HttpException(500, 'Server error');
        }

        $account = $autoresponderInstance
            ->setAccessToken($accessToken->getAccessToken())
            ->getAccount();

        if (is_null($account)) {
            return response(
                'We cannot get your account details from the autoresponder. Please try again.',
                400
            );
        }

        try {
            $userid = CommonUtils::getUserId();
            $integration = Integration::where('type', $autoresponder)
                ->where('user_id', $userid)
                ->first();

            if (!$integration) {
                $integration = new Integration();
                $integration->user_id = $userid;
                $integration->type = $autoresponder;
            }

            $integration->oauth_data = [
                'name' => (string) $account['name'],
                'access_token' => (string) $accessToken->getAccessToken(),
                'refresh_token' => (string) $accessToken->getRefreshToken(),
                'expires_in' => (int) $accessToken->getExpiresIn(),
            ];

            $integration->save();
        } catch (\Throwable $e) {
            Log::error('save access token ' . $e->getMessage());

            throw new HttpException(500, 'Server error');
        }

        return redirect('app/integrations#' . $autoresponder);
    }

    /**
     * Unauthorize an OAuth 2.O autoresponder account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorize(Request $request)
    {
        $autoresponder = trim($request->autoresponder);

        if (mb_strlen($autoresponder) === 0) {
            return response()->json(
                [
                    'error' => 'The "autoresponder" field is not specified.',
                ],
                400
            );
        }

        $userid = CommonUtils::getUserId();

        $integration = Integration::where('type', $autoresponder)
            ->where('user_id', $userid)
            ->first();

        if (is_null($integration)) {
            return response()->json(
                [
                    'error' => 'Autoresponder not found.',
                ],
                400
            );
        }

        $accessToken = $integration->accessToken;

        if (is_null($accessToken)) {
            return response()->json(
                [
                    'error' =>
                    'The autoresponder does not have OAuth data or its data is malformed.',
                ],
                400
            );
        }

        // Check if any VidGens are using this integration before revoking it
        if (
            Vidpop::where('integration', $autoresponder)
            ->where('user_id', $userid)
            ->count()
        ) {
            return response()->json(
                [
                    'error' =>
                    'You cannot unauthorize this autoresponder because one or more VidGens are using it.',
                ],
                400
            );
        }

        $autoresponderInstance = Autoresponder::factory($autoresponder);

        DB::beginTransaction();

        try {
            $integration->delete();
            $autoresponderInstance->setAccessToken($accessToken)->unauthorize();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('unauthorize autoresponder ' . $e->getMessage());

            return response()->json(['error' => 'Server error'], 400);
        }

        return response()->json(['msg' => 'Unauthorized.']);
    }

    public function save(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $integration = Integration::where('type', $request->type)
                ->where('user_id', $userid)
                ->first();
            if (!$integration) {
                $integration = new Integration();
                $integration->user_id = $userid;
                $integration->type = $request->type;
            }
            $integration->data = $request->data;
            $integration->api = $request->api;

            if ($request->type === 'mailchimp') {
                $integration->mailchimp_datacenter =
                    $request->mailchimp_datacenter;
            }

            $integration->save();

            return response()->json(['msg' => 'Saved!'], 200);
        } catch (\Throwable $e) {
            Log::error('Integration save: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 400);
        }
    }

    public function list(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $lists = $this->autoresponderService->getListsOfUser(
                $userid,
                $request->type
            );
        } catch (\Throwable $e) {
            if ($e instanceof AutoresponderServiceException) {
                return response()->json(
                    ['error' => $e->getMessage()],
                    $e->getCode()
                );
            }

            Log::error('Integration list: ' . $e->getMessage());

            return response()->json(['error' => 'Server Error'], 400);
        }

        return response()->json($lists, 200);
    }
}
