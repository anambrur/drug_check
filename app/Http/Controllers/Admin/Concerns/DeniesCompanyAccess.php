<?php

namespace App\Http\Controllers\Admin\Concerns;

trait DeniesCompanyAccess
{
    protected function denyCompanyUsers(): void
    {
        abort_if(auth()->user()?->hasRole('company'), 403);
    }
}
