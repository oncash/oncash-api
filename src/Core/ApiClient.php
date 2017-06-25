<?php

namespace OnCash\Core;

use OnCash\Http\HttpClientInterface;
use OnCash\Exception\ContentFormatException;
use OnCash\Exception\ApiException;

class ApiClient
{
    private $http_client = null;
    private $is_json = true;
    private $base_url = null;
    private $base_content = [];

    /**
     * ApiClient constructor.
     * @param HttpClientInterface $http_client
     * @param $base_url
     * @param array $base_content
     */
    public function __construct(HttpClientInterface $http_client, $base_url, $base_content = [])
    {
        if(!is_array($base_content)) {
            throw new \InvalidArgumentException(sprintf('$base_content must be array, %s given', gettype($base_content)));
        }

        if (!filter_var($base_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL', $base_url));
        }

        $this->http_client = $http_client;
        $this->base_url = $this->formatBaseUrl($base_url);
        $this->base_content = $base_content;
    }

    /**
     * Disable content conversion for this request
     * @return $this
     */
    public function asSource()
    {
        $this->is_json = false;
        return $this;
    }

    /**
     * Enable content JSON conversion for this request
     * @return $this
     */
    public function asJson()
    {
        $this->is_json = true;
        return $this;
    }

    /**
     * @param string $url
     * @param array|string|null $content
     * @return mixed|string
     */
    public function get($url, $content = null)
    {
        return $this->request('GET', $url, $content);
    }

    /**
     * @param string $url
     * @param array|string|null $content
     * @return mixed|string
     */
    public function post($url, $content = null)
    {
        return $this->request('POST', $url, $content);
    }

    /**
     * @param $method
     * @param $url
     * @param $content
     * @return mixed|string
     */
    private function request($method, $url, $content)
    {
        if(!empty($this->base_content)){
            if(is_array($content)) {
                $content = array_merge($this->base_content, $content);
            } else {
                $content .= (strtoupper($method) === 'GET' && !empty($content) ? '&' : '') . http_build_query($this->base_content);
            }
        }

        $url = $this->getAbsoluteUrl($url);

        $body = $this->http_client->request($method, $url, $content);
        $this->handleErrors($body);

        if ($this->is_json) {
            return json_decode($body, true);
        }
        $this->is_json = true;
        return $body;
    }

    /**
     * @param $body
     * @throws ContentFormatException
     */
    private function handleErrors($body)
    {
        $json = json_decode($body);
        $status_code = $this->http_client->getStatusCode();

        if ($status_code >= 400) {
            $exception = "OnCash\\Exception\\" . ApiException::getExceptionFromStatusCode($status_code);
            $message = isset($json->error_message) ? $json->error_message : sprintf('Api return code %d.', $status_code);
            throw new $exception($message);
        }

        if ($this->is_json) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ContentFormatException("Malformed JSON response");
            }
        }
    }

    /**
     * @param string $base_url
     * @return string
     */
    private function formatBaseUrl($base_url)
    {
        return rtrim($base_url, '/') . '/';
    }

    /**
     * @param $url
     * @return string
     */
    private function getAbsoluteUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) ?
            $this->formatBaseUrl($url) :
            $this->base_url . ltrim($url, '/');
    }


}