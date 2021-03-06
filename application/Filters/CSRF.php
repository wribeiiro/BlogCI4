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
use CodeIgniter\Security\Exceptions\SecurityException;
use Config\Services;

/**
 * Class CSRF
 *
 * @package App\Filters
 */
class CSRF implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     *
     * @return mixed
     */
    public function before(RequestInterface $request)
    {
        if ($request->isCLI()) {
            return;
        }

        $security = Services::security();

        try
		{
			$security->CSRFVerify($request);
		}
		catch (SecurityException $e)
		{
			if (config('App')->CSRFRedirect && ! $request->isAJAX())
			{
				return redirect()->back()->with('error', $e->getMessage());
			}

			throw $e;
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
