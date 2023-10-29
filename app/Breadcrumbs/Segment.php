<?php

namespace App\Breadcrumbs;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Segment
{
    protected Request $request;

    protected $segment;

    public function __construct(Request $request, $segment)
    {
        $this->request = $request;
        $this->segment = $segment;
    }

    public function name()
    {
        return Str::title($this->segment);
    }

    public function model()
    {
        return collect($this->request->route()->parameters())
            ->where('slug', $this->segment)->first();
    }

    public function url()
    {
        return url(implode('/', array_slice($this->request->segments(), 0, $this->position() + 1 )));
    }

    public function position()
    {
        return array_search($this->segment, $this->request->segments());
    }
}
