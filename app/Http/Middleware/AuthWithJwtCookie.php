<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\PayloadException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthWithJwtCookie
{
    /**
     * The login endpoint.
     *
     * @var string
     */
    protected $loginEndpoint = '/login';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->setUserFromToken();
        } catch (TokenInvalidException $e) {
            return $this->getErrorResponse('Invalid token.', 400);
        } catch (TokenExpiredException $e) {
            return $this->getErrorResponse('Token expired.', 401);
        } catch (TokenBlacklistedException $e) {
            return $this->getErrorResponse('Token blackliseted.', 401);
        } catch (PayloadException $e) {
            return $this->getErrorResponse('Invalid token payload.', 400);
        }

        return $next($request);
    }

    /**
     * Set the user from the JWT token cookie.
     *
     * @return \App\User|null
     */
    private function setUserFromToken()
    {
        $token = array_key_exists('token', $_COOKIE) ? $_COOKIE['token'] : null;

        return !is_null($token) ? Auth::setToken($token)->user() : null;
    }

    /**
     * Return a new response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\Response
     */
    private function getErrorResponse($message, $statusCode = 401)
    {
        return response(
            $message .
                '<br><a href="' .
                url($this->loginEndpoint) .
                '">Login</a>',
            $statusCode
        );
    }
}
