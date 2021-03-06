<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Class Throttle
 *
 * @package App\Filters
 */
class Throttle implements FilterInterface
{
    /**
     * This is a demo implementation of using the Throttler class
     * to implement rate limiting for your application.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     *
     * @return mixed
     */
    public function before(RequestInterface $request)
    {
        $throttler = Services::throttler();

        // Restrict an IP address to no more
        // than 1 request per second across the
        // entire site.
        if ($throttler->check($request->getIPAddress(), 60, MINUTE) === false) {
            return Services::response()->setStatusCode(429);
        }
    }

    //--------------------------------------------------------------------

    /**
     * We don't have anything to do here.
     *
     * @param \CodeIgniter\HTTP\RequestInterface  $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response)
    {
    }

    //--------------------------------------------------------------------
}
