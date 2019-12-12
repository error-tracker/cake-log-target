<?php

declare(strict_types = 1);

namespace ErrorTracker\Cake;

use ErrorTracker\Client;

/**
 * Error Tracker log target
 *
 * @package   error-tracker/cake-log-target
 * @author    Ade Attwood <ade@practically.io>
 * @copyright 2019 Practically.io
 */
class LogTarget extends \Cake\Log\Engine\BaseLog
{
    /**
     * Your application key from Error Tracker
     *
     * @var string
     */
    public $app_key = null;

    /**
     * The URL of the error tracker application
     *
     * @var string
     */
    public $postUrl = '';

    public function __construct($options = [])
    {
        parent::__construct($options);

        if (!isset($this->_config['client']) && !$this->hasAppKey()) {
            throw new \Exception('app_key must be set');
        }
    }

    /**
     * Test to see if there is a valid app_key
     *
     * @return boolean
     */
    protected function hasAppKey(): bool
    {
        return isset($this->_config['app_key']) && is_string($this->_config['app_key']);
    }

    /**
     * Gets the internal instance of the error tracker client. If it is not set
     * it will set up the default client to interact with the error tracker API
     *
     * @return Client
     */
    public function getClient(): Client
    {
        if (!isset($this->_config['client']) || $this->_config['client'] === null) {
            $this->_config['client'] = new Client($this->_config['app_key']);
        }

        return $this->_config['client'];
    }

    /**
     * Set the internal client
     *
     * @see self::setClient()
     *
     * @return void
     */
    public function setClient(?Client $client): void
    {
        $this->_config['client'] = $client;
    }

    /**
     * Gets the full url for the request
     *
     * @return string
     */
    public function getUrl(): string
    {
        $url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['SERVER_NAME'] ?? 'unknown');

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        return $url . ($_SERVER['REQUEST_URI'] ?? '/unknown');
    }

    /**
     * Sends the log report to the Error Tracker app
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $error = strtok($this->_format($message), "\n");
        preg_match('/^\[?(.*?)[\]:]/', $error, $matches);

        $this->getClient()->report([
            'type' => $level === 'warning' ? 2 : 1,
            'name' => isset($matches[1]) ? $matches[1] : null,
            'description' => $error,
            'text' => $this->_format([$message, $context]),
            'url' => $this->getUrl(),
             'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
        ]);
    }
}
