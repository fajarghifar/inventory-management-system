<?php

namespace App\Breadcrumbs;

use Illuminate\Http\Request;

class Breadcrumbs
{
    protected $request;

    public function __construct(Request $request)
    {
        return $this->request = $request;
    }

    public function segments(): array
    {
        return collect($this->request->segments())->map(function ($segment){
            return new Segment($this->request, $segment);
        })->toArray();
    }
}
