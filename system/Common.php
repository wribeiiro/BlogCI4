<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Common Functions
 *
 * Several application-wide utility methods.
 *
 * @package  CodeIgniter
 * @category Common Functions
 */
//--------------------------------------------------------------------
// Services Convenience Functions
//--------------------------------------------------------------------

if ( ! function_exists('cache'))
{
	/**
	 * A convenience method that provides access to the Cache
	 * object. If no parameter is provided, will return the object,
	 * otherwise, will attempt to return the cached value.
	 *
	 * Examples:
	 *    cache()->save('foo', 'bar');
	 *    $foo = cache('bar');
	 *
	 * @param null|string $key
	 *
	 * @return \CodeIgniter\Cache\CacheInterface|mixed
	 */
	function cache(string $key = null)
	{
		$cache = \Config\Services::cache();

		// No params - return cache object
		if (null === $key)
		{
			return $cache;
		}

		// Still here? Retrieve the value.
		return $cache->get($key);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('config'))
{
	/**
	 * More simple way of getting config instances
	 *
	 * @param string $name
	 * @param bool   $getShared
	 *
	 * @return mixed
	 */
	function config(string $name, bool $getShared = true)
	{
		return \CodeIgniter\Config\Config::get($name, $getShared);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('view'))
{
	/**
	 * Grabs the current RendererInterface-compatible class
	 * and tells it to render the specified view. Simply provides
	 * a convenience method that can be used in Controllers,
	 * libraries, and routed closures.
	 *
	 * NOTE: Does not provide any escaping of the data, so that must
	 * all be handled manually by the developer.
	 *
	 * @param string $name
	 * @param array  $data
	 * @param array  $options Unused - reserved for third-party extensions.
	 *
	 * @return string
	 */
	function view(string $name, array $data = [], array $options = [])
	{
		/**
		 * @var CodeIgniter\View\View $renderer
		 */
		$renderer = Services::renderer();

		$saveData = null;
		if (array_key_exists('saveData', $options) && $options['saveData'] === true)
		{
			$saveData = (bool) $options['saveData'];
			unset($options['saveData']);
		}

		return $renderer->setData($data, 'raw')
						->render($name, $options, $saveData);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('view_cell'))
{
	/**
	 * View cells are used within views to insert HTML chunks that are managed
	 * by other classes.
	 *
	 * @param string      $library
	 * @param null        $params
	 * @param int         $ttl
	 * @param null|string $cacheName
	 *
	 * @return string
	 */
	function view_cell(string $library, $params = null, int $ttl = 0, string $cacheName = null)
	{
		return Services::viewcell()
						->render($library, $params, $ttl, $cacheName);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('env'))
{
	/**
	 * Allows user to retrieve values from the environment
	 * variables that have been set. Especially useful for
	 * retrieving values set from the .env file for
	 * use in config files.
	 *
	 * @param string $key
	 * @param null   $default
	 *
	 * @return mixed
	 */
	function env(string $key, $default = null)
	{
		$value = getenv($key);
		if ($value === false)
		{
			$value = $_ENV[$key] ?? $_SERVER[$key] ?? false;
		}

		// Not found? Return the default value
		if ($value === false)
		{
			return $default;
		}

		// Handle any boolean values
		switch (strtolower($value))
		{
			case 'true':
				return true;
			case 'false':
				return false;
			case 'empty':
				return '';
			case 'null':
				return;
		}

		return $value;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('esc'))
{
	/**
	 * Performs simple auto-escaping of data for security reasons.
	 * Might consider making this more complex at a later date.
	 *
	 * If $data is a string, then it simply escapes and returns it.
	 * If $data is an array, then it loops over it, escaping each
	 * 'value' of the key/value pairs.
	 *
	 * Valid context values: html, js, css, url, attr, raw, null
	 *
	 * @param array|string $data
	 * @param string       $context
	 * @param string       $encoding
	 *
	 * @return array|string
	 */
	function esc($data, $context = 'html', $encoding = null)
	{
		if (is_array($data))
		{
			foreach ($data as $key => &$value)
			{
				$value = esc($value, $context);
			}
		}

		if (is_string($data))
		{
			$context = strtolower($context);

			// Provide a way to NOT escape data since
			// this could be called automatically by
			// the View library.
			if (empty($context) || $context == 'raw')
			{
				return $data;
			}

			if ( ! in_array($context, ['html', 'js', 'css', 'url', 'attr'], true))
			{
				throw new \InvalidArgumentException('Invalid escape context provided.');
			}

			if ($context == 'attr')
			{
				$method = 'escapeHtmlAttr';
			}
			else
			{
				$method = 'escape' . ucfirst($context);
			}

			// @todo Optimize this to only load a single instance during page request.
			$escaper = new \Zend\Escaper\Escaper($encoding);

			$data = $escaper->$method($data);
		}

		return $data;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('session'))
{
	/**
	 * A convenience method for accessing the session instance,
	 * or an item that has been set in the session.
	 *
	 * Examples:
	 *    session()->set('foo', 'bar');
	 *    $foo = session('bar');
	 *
	 * @param string $val
	 *
	 * @return null|\CodeIgniter\Session\Session|mixed
	 */
	function session($val = null)
	{
		$session = \Config\Services::session();

		// Returning a single item?
		if (is_string($val))
		{
			return $session->get($val);
		}

		return $session;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('timer'))
{
	/**
	 * A convenience method for working with the timer.
	 * If no parameter is passed, it will return the timer instance,
	 * otherwise will start or stop the timer intelligently.
	 *
	 * @param null|string $name
	 *
	 * @return \CodeIgniter\Debug\Timer|mixed
	 */
	function timer(string $name = null)
	{
		$timer = \Config\Services::timer();

		if (empty($name))
		{
			return $timer;
		}

		if ($timer->has($name))
		{
			return $timer->stop($name);
		}

		return $timer->start($name);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('service'))
{
	/**
	 * Allows cleaner access to the Services Config file.
	 * Always returns a SHARED instance of the class, so
	 * calling the function multiple times should always
	 * return the same instance.
	 *
	 * These are equal:
	 *  - $timer = service('timer')
	 *  - $timer = \CodeIgniter\Config\Services::timer();
	 *
	 * @param string $name
	 * @param array  ...$params
	 *
	 * @return mixed
	 */
	function service(string $name, ...$params)
	{
		return Services::$name(...$params);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('single_service'))
{
	/**
	 * Allow cleaner access to a Service.
	 * Always returns a new instance of the class.
	 *
	 * @param string     $name
	 * @param null|array $params
	 *
	 * @return mixed
	 */
	function single_service(string $name, ...$params)
	{
		// Ensure it's NOT a shared instance
		array_push($params, false);

		return Services::$name(...$params);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('lang'))
{
	/**
	 * A convenience method to translate a string and format it
	 * with the intl extension's MessageFormatter object.
	 *
	 * @param string $line
	 * @param array  $args
	 * @param string $locale
	 *
	 * @return string
	 */
	function lang(string $line, array $args = [], string $locale = null)
	{
		return Services::language($locale)
						->getLine($line, $args);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('log_message'))
{
	/**
	 * A convenience/compatibility method for logging events through
	 * the Log system.
	 *
	 * Allowed log levels are:
	 *  - emergency
	 *  - alert
	 *  - critical
	 *  - error
	 *  - warning
	 *  - notice
	 *  - info
	 *  - debug
	 *
	 * @param string     $level
	 * @param string     $message
	 * @param null|array $context
	 *
	 * @return mixed
	 */
	function log_message(string $level, string $message, array $context = [])
	{
		// When running tests, we want to always ensure that the
		// TestLogger is running, which provides utilities for
		// for asserting that logs were called in the test code.
		if (ENVIRONMENT == 'testing')
		{
			$logger = new \Tests\Support\Log\TestLogger(new \Config\Logger());

			return $logger->log($level, $message, $context);
		}

		// @codeCoverageIgnoreStart
		return Services::logger(true)
						->log($level, $message, $context);
		// @codeCoverageIgnoreEnd
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('is_cli'))
{
	/**
	 * Is CLI?
	 *
	 * Test to see if a request was made from the command line.
	 *
	 * @return    bool
	 */
	function is_cli()
	{
		return (\PHP_SAPI === 'cli' || defined('STDIN'));
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('route_to'))
{
	/**
	 * Given a controller/method string and any params,
	 * will attempt to build the relative URL to the
	 * matching route.
	 *
	 * NOTE: This requires the controller/method to
	 * have a route defined in the routes Config file.
	 *
	 * @param string $method
	 * @param array       ...$params
	 *
	 * @return false|string
	 */
	function route_to(string $method, ...$params): string
	{
		$routes = Services::routes();

		return $routes->reverseRoute($method, ...$params);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param   string $str
	 * @param   bool   $url_encoded
	 *
	 * @return  string
	 */
	function remove_invisible_characters($str, $url_encoded = true)
	{
		$non_displayables = [];

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';   // url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		} while ($count);

		return $str;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('helper'))
{
	/**
	 * Loads a helper file into memory. Supports namespaced helpers,
	 * both in and out of the 'helpers' directory of a namespaced directory.
	 *
	 * @param array|string $filenames
	 */
	function helper($filenames)
	{
		$loader = Services::locator(true);

		if ( ! is_array($filenames))
		{
			$filenames = [$filenames];
		}

		foreach ($filenames as $filename)
		{
			if (strpos($filename, '_helper') === false)
			{
				$filename .= '_helper';
			}

			$path = $loader->locateFile($filename, 'Helpers');

			if ( ! empty($path))
			{
				include_once $path;
			}
		}
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('app_timezone'))
{
	/**
	 * Returns the timezone the application has been set to display
	 * dates in. This might be different than the timezone set
	 * at the server level, as you often want to stores dates in UTC
	 * and convert them on the fly for the user.
	 *
	 * @return string
	 */
	function app_timezone()
	{
		$config = config(\Config\App::class);

		return $config->appTimezone;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('csrf_token'))
{
	/**
	 * Returns the CSRF token name.
	 * Can be used in Views when building hidden inputs manually,
	 * or used in javascript vars when using APIs.
	 *
	 * @return string
	 */
	function csrf_token()
	{
		$config = config(\Config\App::class);

		return $config->CSRFTokenName;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('csrf_hash'))
{
	/**
	 * Returns the current hash value for the CSRF protection.
	 * Can be used in Views when building hidden inputs manually,
	 * or used in javascript vars for API usage.
	 *
	 * @return string
	 */
	function csrf_hash()
	{
		$security = Services::security(null, true);

		return $security->getCSRFHash();
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('csrf_field'))
{
	/**
	 * Generates a hidden input field for use within manually generated forms.
	 *
	 * @return string
	 */
	function csrf_field()
	{
		return '<input type="hidden" name="' . csrf_token() . '" value="' . csrf_hash() . '">';
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('force_https'))
{
	/**
	 * Used to force a page to be accessed in via HTTPS.
	 * Uses a standard redirect, plus will set the HSTS header
	 * for modern browsers that support, which gives best
	 * protection against man-in-the-middle attacks.
	 *
	 * @see https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security
	 *
	 * @param int               $duration How long should the SSL header be set for? (in seconds)
	 *                                    Defaults to 1 year.
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 *
	 * Not testable, as it will exit!
	 *
	 * @throws \CodeIgniter\HTTP\RedirectException
	 * @codeCoverageIgnore
	 */
	function force_https(int $duration = 31536000, RequestInterface $request = null, ResponseInterface $response = null)
	{
		if (null === $request)
		{
			$request = Services::request(null, true);
		}
		if (null === $response)
		{
			$response = Services::response(null, true);
		}

		if (is_cli() || $request->isSecure())
		{
			return;
		}

		// If the session library is loaded, we should regenerate
		// the session ID for safety sake.
		if (class_exists('Session', false))
		{
			Services::session(null, true)
					->regenerate();
		}

		$uri = $request->uri;
		$uri->setScheme('https');

		$uri = \CodeIgniter\HTTP\URI::createURIString(
						$uri->getScheme(),
		    $uri->getAuthority(true),
		    $uri->getPath(), // Absolute URIs should use a "/" for an empty path
											$uri->getQuery(),
		    $uri->getFragment()
		);

		// Set an HSTS header
		$response->setHeader('Strict-Transport-Security', 'max-age=' . $duration);
		$response->redirect($uri);
		exit();
	}
}

//--------------------------------------------------------------------

if (! function_exists('old'))
{
	/**
	 * Provides access to "old input" that was set in the session
	 * during a redirect()->withInput().
	 *
	 * @param string       $key
	 * @param null         $default
	 * @param bool|string  $escape
	 *
	 * @return null|mixed
	 */
	function old(string $key, $default = null, $escape = 'html')
	{
		$request = Services::request();

		$value = $request->getOldInput($key);

		// Return the default value if nothing
		// found in the old input.
		if (null === $value)
		{
			return $default;
		}

		// If the result was serialized array or string, then unserialize it for use...
		if (strpos($value, 'a:') === 0 || strpos($value, 's:') === 0)
		{
			$value = unserialize($value);
		}

		return $escape === false ? $value : esc($value, $escape);
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('redirect'))
{
	/**
	 * Convenience method that works with the current global $request and
	 * $router instances to redirect using named/reverse-routed routes
	 * to determine the URL to go to. If nothing is found, will treat
	 * as a traditional redirect and pass the string in, letting
	 * $response->redirect() determine the correct method and code.
	 *
	 * If more control is needed, you must use $response->redirect explicitly.
	 *
	 * @param string $uri
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	function redirect(string $uri=null)
	{
		$response = Services::redirectResponse(null, true);

		if (! empty($uri))
		{
			return $response->to($uri);
		}

		return $response;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('stringify_attributes'))
{
	/**
	 * Stringify attributes for use in HTML tags.
	 *
	 * Helper function used to convert a string, array, or object
	 * of attributes to a string.
	 *
	 * @param   mixed $attributes string, array, object
	 * @param   bool  $js
	 *
	 * @return  string
	 */
	function stringify_attributes($attributes, $js = false): string
	{
		$atts = '';

		if (empty($attributes))
		{
			return $atts;
		}

		if (is_string($attributes))
		{
			return ' ' . $attributes;
		}

		$attributes = (array) $attributes;

		foreach ($attributes as $key => $val)
		{
			$atts .= ($js) ? $key . '=' . esc($val, 'js') . ',' : ' ' . $key . '="' . esc($val, 'attr') . '"';
		}

		return rtrim($atts, ',');
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('is_really_writable'))
{
	/**
	 * Tests for file writability
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link    https://bugs.php.net/bug.php?id=54709
	 *
	 * @param   string $file
	 *
	 * @return  bool
	 *
	 * @codeCoverageIgnore	Not practical to test, as travis runs on linux
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (\DIRECTORY_SEPARATOR === '/' || ! ini_get('safe_mode'))
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/') . '/' . bin2hex(random_bytes(16));
			if (($fp = @fopen($file, 'ab')) === false)
			{
				return false;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);

			return true;
		}
		if ( ! is_file($file) || ( $fp = @fopen($file, 'ab')) === false)
		{
			return false;
		}

		fclose($fp);

		return true;
	}
}

//--------------------------------------------------------------------

if ( ! function_exists('slash_item'))
{
	//Unlike CI3, this function is placed here because
	//it's not a config, or part of a config.
	/**
	 * Fetch a config file item with slash appended (if not empty)
	 *
	 * @param   string $item Config item name
	 *
	 * @return  null|string The configuration item or NULL if
	 * the item doesn't exist
	 */
	function slash_item($item)
	{
		$config = config(\Config\App::class);
		$configItem = $config->{$item};

		if ( ! isset($configItem) || empty(trim($configItem)))
		{
			return $configItem;
		}

		return rtrim($configItem, '/') . '/';
	}
}
//--------------------------------------------------------------------

if ( ! function_exists('function_usable'))
{
	/**
	 * Function usable
	 *
	 * Executes a function_exists() check, and if the Suhosin PHP
	 * extension is loaded - checks whether the function that is
	 * checked might be disabled in there as well.
	 *
	 * This is useful as function_exists() will return FALSE for
	 * functions disabled via the *disable_functions* php.ini
	 * setting, but not for *suhosin.executor.func.blacklist* and
	 * *suhosin.executor.disable_eval*. These settings will just
	 * terminate script execution if a disabled function is executed.
	 *
	 * The above described behavior turned out to be a bug in Suhosin,
	 * but even though a fix was commited for 0.9.34 on 2012-02-12,
	 * that version is yet to be released. This function will therefore
	 * be just temporary, but would probably be kept for a few years.
	 *
	 * @link	http://www.hardened-php.net/suhosin/
	 * @param	string	$function_name	Function to check for
	 * @return	bool	TRUE if the function exists and is safe to call,
	 * 			FALSE otherwise.
	 *
	 * @codeCoverageIgnore	This is too exotic
	 */
	function function_usable($function_name)
	{
		static $_suhosin_func_blacklist;

		if (function_exists($function_name))
		{
			if ( ! isset($_suhosin_func_blacklist))
			{
				$_suhosin_func_blacklist = extension_loaded('suhosin') ? explode(',', trim(ini_get('suhosin.executor.func.blacklist'))) : [];
			}

			return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
		}

		return FALSE;
	}
}

//--------------------------------------------------------------------

if (! function_exists('dd'))
{
	/**
	 * Prints a Kint debug report and exits.
	 *
	 * @param array ...$vars
	 *
	 * @codeCoverageIgnore	Can't be tested ... exits
	 */
	function dd(...$vars)
	{
		Kint::dump(...$vars);
		exit;
	}
}
