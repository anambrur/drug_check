<?php

namespace Tests\Unit\Services\RandomSelection;

use App\Services\RandomSelectionService;
use Tests\TestCase;

class SecureShuffleTest extends TestCase
{
    public function test_secure_shuffle_is_permutation_of_input(): void
    {
        $service = app(RandomSelectionService::class);
        $input = ['A', 'B', 'C', 'D', 'E'];

        $shuffled = $service->secureShuffle($input);

        $this->assertCount(5, $shuffled);
        $this->assertSame(
            collect($input)->sort()->values()->all(),
            collect($shuffled)->sort()->values()->all()
        );
    }

    public function test_secure_rand_stays_within_closed_interval(): void
    {
        $service = app(RandomSelectionService::class);

        for ($i = 0; $i < 50; $i++) {
            $value = $service->secureRand(0, 4);
            $this->assertGreaterThanOrEqual(0, $value);
            $this->assertLessThanOrEqual(4, $value);
        }
    }
}
