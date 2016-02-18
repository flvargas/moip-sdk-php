<?php

namespace Moip;

use Moip\Resource\Customer;
use Moip\Resource\Entry;
use Moip\Resource\Multiorders;
use Moip\Resource\Orders;
use Moip\Resource\Payment;
use Requests_Session;

class Moip
{
    /**
     * endpoint of production.
     *
     * @const string
     */
    const ENDPOINT_PRODUCTION = 'api.moip.com.br';
    /**
     * endpoint of sandbox.
     *
     * @const string
     */
    const ENDPOINT_SANDBOX = 'sandbox.moip.com.br';

    /**
     * Client name.
     *
     * @const string
     **/
    const CLIENT = 'Moip SDK';

    /**
     * Authentication that will be added to the header of request.
     *
     * @var \Moip\MoipAuthentication
     */
    private $moipAuthentication;

    /**
     * Endpoint of request.
     *
     * @var \Moip\Moip::ENDPOINT_PRODUCTION|\Moip\Moip::ENDPOINT_SANDBOX
     */
    private $endpoint;

    /**
     * @var Requests_Session HTTP session configured to use the moip API.
     */
    private $session;

    /**
     * Create a new aurhentication with the endpoint.
     *
     * @param \Moip\MoipAuthentication               $moipAuthentication
     * @param \Moip\Moip::ENDPOINT_PRODUCTION|string $endpoint
     */
    public function __construct(MoipAuthentication $moipAuthentication, $endpoint = self::ENDPOINT_PRODUCTION)
    {
        $this->moipAuthentication = $moipAuthentication;
        $this->endpoint = $endpoint;
        $this->createNewSession();
    }

    /**
     * Creates a new Request_Session (one is created at construction).
     *
     * @param float $timeout         How long should we wait for a response?(seconds with a millisecond precision, default: 30, example: 0.01).
     * @param float $connect_timeout How long should we wait while trying to connect? (seconds with a millisecond precision, default: 10, example: 0.01)
     */
    public function createNewSession($timeout = 30.0, $connect_timeout = 30.0)
    {
        $locale = setlocale(LC_ALL, null);
        if (function_exists('posix_uname')) {
            $uname = posix_uname();
            $user_agent = sprintf('Mozilla/4.0 (compatible; %s; PHP/%s %s; %s; %s; %s)',
                self::CLIENT, PHP_SAPI, PHP_VERSION, $uname['sysname'], $uname['machine'], $locale);
        } else {
            $user_agent = sprintf('Mozilla/4.0 (compatible; %s; PHP/%s %s; %s; %s)',
                self::CLIENT, PHP_SAPI, PHP_VERSION, PHP_OS, $locale);
        }
        $sess = new Requests_Session($this->endpoint);
        $sess->options['auth'] = $this->moipAuthentication;
        $sess->options['timeout'] = $timeout;
        $sess->options['connect_timeout'] = $connect_timeout;
        $sess->options['useragent'] = $user_agent;
        $this->session = $sess;
    }

    /**
     * Returns the http session created.
     *
     * @return Requests_Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Create a new Customer instance.
     *
     * @return \Moip\Resource\Customer
     */
    public function customers()
    {
        return new Customer($this);
    }

    /**
     * Create a new Entry instance.
     *
     * @return \Moip\Resource\Entry
     */
    public function entries()
    {
        return new Entry($this);
    }

    /**
     * Create a new Orders instance.
     *
     * @return \Moip\Resource\Orders
     */
    public function orders()
    {
        return new Orders($this);
    }

    /**
     * Create a new Payment instance.
     *
     * @return \Moip\Resource\Payment
     */
    public function payments()
    {
        return new Payment($this);
    }

    /**
     * Create a new Multiorders instance.
     *
     * @return \Moip\Resource\Multiorders
     */
    public function multiorders()
    {
        return new Multiorders($this);
    }

    /**
     * Get the endpoint.
     *
     * @return \Moip\Moip::ENDPOINT_PRODUCTION|\Moip\Moip::ENDPOINT_SANDBOX
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
