<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\PortfolioTestCheckoutRequest;
use Illuminate\Routing\Route;
use Tests\TestCase;

class PortfolioTestCheckoutRequestTest extends TestCase
{
    public function test_dot_route_forces_dot_test_to_t_even_when_client_posts_f(): void
    {
        $request = $this->makeCheckoutRequest(
            'frontend.portfolio-test.checkout.dot',
            'portfolio-test/checkout/dot',
            [
                'test_type' => 'non_dot',
                'dot_test' => 'F',
                'employee_id' => 1,
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com',
                'primary_id' => 'DL123',
                'testing_authority' => 'FMCSA',
                'reason_for_test_id' => 1,
                'is_physical' => 'true',
                'is_ebat' => 'false',
            ]
        );

        $this->invokePrepare($request);

        $this->assertSame('dot', $request->input('test_type'));
        $this->assertSame('T', $request->input('dot_test'));
        // Without a portfolio_id lookup, client-supplied physical flags are ignored and reset.
        $this->assertSame('false', $request->input('is_physical'));
    }

    public function test_non_dot_route_forces_dot_test_to_f_even_when_client_posts_t(): void
    {
        $request = $this->makeCheckoutRequest(
            'frontend.portfolio-test.checkout.non-dot',
            'portfolio-test/checkout/non-dot',
            [
                'test_type' => 'dot',
                'dot_test' => 'T',
                'testing_authority' => 'FMCSA',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com',
                'primary_id' => 'DL123',
                'reason_for_test_id' => 1,
                'is_physical' => 'false',
                'is_ebat' => 'false',
            ]
        );

        $this->invokePrepare($request);

        $this->assertSame('non_dot', $request->input('test_type'));
        $this->assertSame('F', $request->input('dot_test'));
    }

    public function test_return_to_duty_forces_observed_collection(): void
    {
        $request = $this->makeCheckoutRequest(
            'frontend.portfolio-test.checkout.dot',
            'portfolio-test/checkout/dot',
            [
                'test_type' => 'dot',
                'dot_test' => 'T',
                'employee_id' => 1,
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.com',
                'primary_id' => 'DL123',
                'testing_authority' => 'FMCSA',
                'reason_for_test_id' => 6,
                'observed_requested' => 'N',
                'is_physical' => 'false',
                'is_ebat' => 'false',
            ]
        );

        $this->invokePrepare($request);

        $this->assertSame('Y', $request->input('observed_requested'));
    }

    private function makeCheckoutRequest(string $routeName, string $uri, array $payload): PortfolioTestCheckoutRequest
    {
        $request = PortfolioTestCheckoutRequest::create('/' . $uri, 'POST', $payload);

        $request->setRouteResolver(function () use ($request, $routeName, $uri) {
            $route = new Route('POST', $uri, []);
            $route->bind($request);
            $route->name($routeName);

            return $route;
        });

        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        return $request;
    }

    private function invokePrepare(PortfolioTestCheckoutRequest $request): void
    {
        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->invoke($request);
    }
}
