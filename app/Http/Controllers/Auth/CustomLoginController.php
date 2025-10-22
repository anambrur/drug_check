<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class CustomLoginController extends Controller
{
    /**
     * Show the custom login view with portfolio ID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $portfolioId
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request, $portfolioId)
    {
        // Store portfolio ID in session for use after login
        session(['login_redirect_portfolio' => $portfolioId]);

        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate with portfolio context
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @param  int  $portfolioId
     * @return mixed
     */
    public function store(LoginRequest $request, $portfolioId)
    {
        // Store portfolio ID from route parameter in session
        session(['login_redirect_portfolio' => $portfolioId]);

        return $this->loginPipeline($request)->then(function ($request) {
            return $this->handlePostAuthentication($request);
        });
    }

    /**
     * Handle post-authentication redirect
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function handlePostAuthentication(Request $request)
    {
        // Check if we have a portfolio ID stored in session
        $portfolioId = session('login_redirect_portfolio');

        if ($portfolioId) {
            // Clear the session value
            session()->forget('login_redirect_portfolio');

            // Redirect to portfolio application form or next step
            return redirect()->route('dot-test.index', $portfolioId);
        }

        return app(LoginResponse::class);
    }

    /**
     * Get the authentication pipeline instance
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        // Simplified pipeline without two-factor authentication references
        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
}
