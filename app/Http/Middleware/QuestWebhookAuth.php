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

        [$providedUser, $providedPass] = $this->extractCredentials($request);

        if (
            $providedUser === null
            || $providedPass === null
            || !hash_equals($username, $providedUser)
            || !hash_equals($password, $providedPass)
        ) {
            return $this->unauthorized();
        }

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        return $next($request);
    }

    /**
     * Spec §3.1 / §3.2 show credentials as SOAP method parameters. Some Quest
     * deployments instead send HTTP Basic. Accept either.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function extractCredentials(Request $request): array
    {
        $authHeader = $request->header('Authorization', '');
        if (str_starts_with($authHeader, 'Basic ')) {
            $decoded = base64_decode(substr($authHeader, 6), true);
            if ($decoded !== false && str_contains($decoded, ':')) {
                return explode(':', $decoded, 2);
            }
        }

        $body = $request->getContent();
        if ($body === '') {
            return [null, null];
        }

        // Local-name match so prefixed SOAP tags still work.
        $user = null;
        $pass = null;

        if (preg_match('/<(?:\w+:)?username[^>]*>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*<\/(?:\w+:)?username>/is', $body, $m)) {
            $user = html_entity_decode(trim($m[1]), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }

        if (preg_match('/<(?:\w+:)?password[^>]*>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*<\/(?:\w+:)?password>/is', $body, $m)) {
            $pass = html_entity_decode(trim($m[1]), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }

        return [$user, $pass];
    }

    private function unauthorized(): Response
    {
        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Quest Webhook"',
        ]);
    }
}
