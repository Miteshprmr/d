<?php

namespace App\Api\V1\Http\Controllers;

class HomeController extends ApiController
{
    /**
     * Get the application home route response.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        return $this->respondOk([
            'status' => 'All good!'
        ]);
    }

    /**
     * Catch all undefined API routes and throw an appropriate error.
     */
    public function catchAll()
    {
        abort(404, '404 API route not found.');
    }
}
