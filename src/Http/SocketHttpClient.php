<?php

namespace OnCash\Http;

class SocketHttpClient implements HttpClientInterface
{
    private $headers = null;
    private $status_code = null;
    private $status_text = null;

    private static $methods = ["GET", "POST", "PUT", "DELETE"];
    private $options = [
        'http' => [
            'method' => "GET",
            'ignore_errors' => 1,
            'header' => "Content-Type: application/json\r\nX-Requested-With: XMLHttpRequest"
        ]
    ];

    /**
     * SocketHttpClient constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (!is_null($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * @param $method
     * @param $url
     * @param array|string|null $content
     * @return string
     */
    public function request($method, $url, $content = null)
    {
        $method = strtoupper($method);

        if (!in_array($method, self::$methods)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" not supported.', $method));
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL', $url));
        }

        if (is_array($content)) {
            $content = http_build_query($content);
        }

        if($method === 'GET') {
            $url .= '?' . $content;
            $content = null;
        }

        $this->options['http']['method'] = $method;
        $this->options['http']['content'] = $content;
        $context = stream_context_create($this->options);
        $body = file_get_contents($url, NULL, $context);
        $this->headers = $this->parseHeaders($http_response_header);
        return (string)$body;
    }

    /**
     * @param null $key
     * @return null|void
     * @throws \Exception
     */
    public function getHeaders($key = null)
    {
        if (is_null($this->headers)) {
            throw new \Exception("No request has been sent");
        }
        if (!is_null($key)) {
            if (isset($this->headers[$key])) {
                return $this->headers[$key];
            }
            return;
        }
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return array
     */
    private function parseHeaders(array $headers)
    {
        $result = [];
        if ('HTTP' === substr($headers[0], 0, 4)) {
            list(, $status_code, $status_text) = explode(' ', $headers[0]);
            $this->status_code = intval($status_code);
            $this->status_text = $status_text;
            unset($headers[0]);
        }
        foreach ($headers as $header) {
            $part = preg_split('/:\s*/', $header);
            $result[strtolower($part[0])] = $part[1];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getStatusCode()
    {
        if (is_null($this->status_code)) {
            throw new \Exception("No request has been sent");
        }
        return $this->status_code;
    }

    /**
     * @return null
     */
    public function getStatusText()
    {
        return $this->status_text;
    }

    /**
     * @param $method
     * @param $arguments
     * @return string
     * @throws \Exception
     */
    public static function __callStatic($method, $arguments)
    {
        $method = strtoupper($method);

        if (!in_array($method, self::$methods)) {
            throw new \Exception(sprintf('Method "%s" is not defined.', $method));
        }

        if (!isset($arguments[0])) {
            throw new \InvalidArgumentException('Argument $url required, but not passed.');
        }

        return (new self)->request($method, $arguments[0], (isset($arguments[1]) ? $arguments[1] : null));

    }
}