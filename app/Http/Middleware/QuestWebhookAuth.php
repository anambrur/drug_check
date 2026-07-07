<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class QuestWebhookAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction() && !$request->secure()) {
            return response('HTTPS required', 403);
        }

        $username = (string) config('services.quest.webhook.username');
        $password = (string) config('services.quest.webhook.password');

        if ($username === '' || $password === '') {
            return response('Webhook not configured', 503);
        }

        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Basic ')) {
            return $this->unauthorized();
        }

        $decoded = base64_decode(substr($authHeader, 6), true);
        if ($decoded === false || !str_contains($decoded, ':')) {
            return $this->unauthorized();
        }

        [$providedUser, $providedPass] = explode(':', $decoded, 2);

        if (!hash_equals($username, $providedUser) || !hash_equals($password, $providedPass)) {
            return $this->unauthorized();
        }

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        return $next($request);
    }

    private function unauthorized(): Response
    {
        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Quest Webhook"',
        ]);
    }
}
