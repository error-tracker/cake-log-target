<?php

declare(strict_types = 1);

namespace ErrorTracker\Cake\Tests;

use Cake\Log\Log;
use ErrorTracker\Client;

/**
 * Error Tracker log target
 *
 * @package   error-tracker/cake-log-target
 * @author    Ade Attwood <ade@practically.io>
 * @copyright 2019 Practically.io
 */
class ErrorTrackerLogTargetTest extends BaseTestCase
{

    protected function tearDown(): void
    {
        if (Log::getConfig('error_tracker')) {
            Log::drop('error_tracker');
        }
    }

    public function testNoAppKey(): void
    {
        try {
            Log::setConfig('error_tracker', [
                'className' => 'ErrorTracker\Cake\LogTarget',
                'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            ]);

            Log::write('error', 'This is an error');

            $this->fail('Exception must be thrown you dont set an application key');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'app_key must be set');

            return;
        }

         $this->fail('An Exception was not thrown');
    }

    public function testBasicReport(): void
    {
        $http = $this->mockHttp()->getMock();
        $http->expects($this->once())
            ->method('post')
            ->willReturn(null)
            ->with(
                '/report',
                [
                    'app_key' => 'CAKE_APP_KEY',
                    'description' => 'This is an error',
                    'text' => "Array\n(\n    [scope] => Array\n        (\n        )\n\n)\n",
                    'type' => 1,
                    'user_agent' => '',
                    'ip' => '',
                    'url' => 'my/test/request',
                ]
            );

        Log::setConfig('error_tracker', [
            'className' => 'ErrorTracker\Cake\LogTarget',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            'app_key' => 'CAKE_APP_KEY',
            'client' => new Client('CAKE_APP_KEY', $http),
        ]);


        Log::write('error', 'This is an error');
    }

    public function testReportArray(): void
    {
        $http = $this->mockHttp()->getMock();
        $http->expects($this->once())
            ->method('post')
            ->willReturn(null)
            ->with(
                '/report',
                [
                    'app_key' => 'CAKE_APP_KEY',
                    'description' => "Array\n(\n    [one] => one\n    [two] => two\n)\n",
                    'text' => "Array\n(\n    [scope] => Array\n        (\n        )\n\n)\n",
                    'type' => 1,
                    'user_agent' => '',
                    'ip' => '',
                    'url' => 'my/test/request',
                ]
            );

        Log::setConfig('error_tracker', [
            'className' => 'ErrorTracker\Cake\LogTarget',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            'app_key' => 'CAKE_APP_KEY',
            'client' => new Client('CAKE_APP_KEY', $http),
        ]);


        Log::write('error', [
            'one' => 'one',
            'two' => 'two',
        ]);
    }

    public function testReportException(): void
    {
        $http = $this->mockHttp()->getMock();
        $http->expects($this->once())
            ->method('post')
            ->will($this->returnCallback(function ($url, $data) {
                if ($url !== '/report') {
                    throw new \Exception('Url is not "/report"');
                }

                $args = [
                    'app_key' => 'CAKE_APP_KEY',
                    'type' => 1,
                    'user_agent' => '',
                    'ip' => '',
                    'url' => 'my/test/request',
                ];

                foreach ($args as $key => $value) {
                    if ($data[$key] !== $value) {
                        throw new \Exception("Invalid value '{$data['value']}' for '{$key}'");
                    }
                }

                if (!preg_match('/This is an exception/', $data['description'])) {
                    throw new \Exception("Invalid description");
                }
            }));

        Log::setConfig('error_tracker', [
            'className' => 'ErrorTracker\Cake\LogTarget',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            'app_key' => 'CAKE_APP_KEY',
            'client' => new Client('CAKE_APP_KEY', $http),
        ]);


        Log::write('error', new \Exception('This is an exception'));
    }

    public function testReportWithContext(): void
    {
        $http = $this->mockHttp()->getMock();
        $http->expects($this->once())
            ->method('post')
            ->will($this->returnCallback(function ($url, $data) {
                if ($url !== '/report') {
                    throw new \Exception('Url is not "/report"');
                }
            }));

        Log::setConfig('error_tracker', [
            'className' => 'ErrorTracker\Cake\LogTarget',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
            'app_key' => 'CAKE_APP_KEY',
            'client' => new Client('CAKE_APP_KEY', $http),
        ]);


        Log::write('error', 'This is the error', ['one' => 'two']);
    }
}
