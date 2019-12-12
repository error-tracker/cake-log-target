<?php

declare(strict_types = 1);

namespace ErrorTracker\Cake\Tests;

use ErrorTracker\Http;
use PHPUnit\Framework\MockObject\MockBuilder;

/**
 * Error Tracker log target
 *
 * @package   error-tracker/cake-log-target
 * @author    Ade Attwood <ade@practically.io>
 * @copyright 2019 Practically.io
 */
class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Creates a mock of the http class
     *
     */
    public function mockHttp(): MockBuilder
    {
        Http::$trackerUrl = 'http:localhost';
        $_SERVER['REQUEST_URI'] = '/my/test/request';

        return $this->getMockBuilder('Http')->setMethods(['post']);
    }
}
