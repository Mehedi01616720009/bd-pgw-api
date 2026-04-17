<?php

namespace Core\Http;

abstract class Middleware
{
    /**
     * Handle the request
     * Return true to continue, false or Response to stop
     */
    abstract public function handle(Request $request);
}
