<?php

namespace OnCash\Http;

interface HttpClientInterface
{
    /**
     * @param $method
     * @param $url
     * @param array|string|null $content
     * @return string
     */
    public function request($method, $url, $content = null);

    /**
     * @param null $key
     * @return null|void
     * @throws \Exception
     */
    public function getHeaders($key = null);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options);

    /**
     * @return int
     * @throws \Exception
     */
    public function getStatusCode();

    /**
     * @return null
     */
    public function getStatusText();
}