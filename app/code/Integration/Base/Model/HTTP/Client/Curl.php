<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Integration\Base\Model\HTTP\Client;

/**
 * Class to work with HTTP protocol using curl library
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    /**
     * Max supported protocol by curl CURL_SSLVERSION_TLSv1_2
     * @var int
     */
    private $sslVersion;

    const COOKIE_FOLDER = '/pub/integration';

    const COOKIE_FILE_NAME = '/myobcookieall.txt';

    /**
     * Returns Cookie file with path
     *
     *
     * @return String
     */
    public function getCookieFileDir()
    {
        return BP . self::COOKIE_FOLDER . self::COOKIE_FILE_NAME;
    }

    /**
     * Make PUT request
     *
     * String type was added to parameter $param in order to support sending JSON or XML requests.
     *
     * @param string $uri
     * @param array|string $params
     * @return void
     *
     * @see \Magento\Framework\HTTP\Client#put($uri, $params)
     */
    public function put($uri, $params)
    {
        $this->makeRequest("PUT", $uri, $params);
    }

    /**
     * Make request
     *
     * String type was added to parameter $param in order to support sending JSON or XML requests.
     * This feature was added base on Community Pull Request https://github.com/magento/magento2/pull/8373
     *
     * @param string $method
     * @param string $uri
     * @param array|string $params - use $params as a string in case of JSON or XML POST request.
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function makeRequest($method, $uri, $params = [])
    {
        $this->_ch = curl_init();
        $this->curlOption(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP | CURLPROTO_FTPS);
        $this->curlOption(CURLOPT_URL, $uri);
        if ($method == 'POST') {
            $this->curlOption(CURLOPT_POST, 1);
            $this->curlOption(CURLOPT_POSTFIELDS, is_array($params) ? json_encode($params) : $params);
        } elseif ($method == "GET") {
            $this->curlOption(CURLOPT_HTTPGET, 1);
        } elseif ($method == 'PUT') {
            $this->curlOption(CURLOPT_CUSTOMREQUEST, 'PUT');
            $this->curlOption(CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
        } else {
            $this->curlOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        if (count($this->_headers)) {
            $heads = [];
            foreach ($this->_headers as $k => $v) {
                $heads[] = $k . ': ' . $v;
            }
            $this->curlOption(CURLOPT_HTTPHEADER, $heads);
        }
        
        /*$this->curlOption(CURLOPT_COOKIEJAR, $this->getCookieFileDir());
        $this->curlOption(CURLOPT_COOKIEFILE, $this->getCookieFileDir());*/
        
        if (count($this->_cookies)) {
            $cookies = [];
            foreach ($this->_cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            $this->curlOption(CURLOPT_COOKIE, implode(";", $cookies));
        }

        if ($this->_timeout) {
            $this->curlOption(CURLOPT_TIMEOUT, $this->_timeout);
        }

        if ($this->_port != 80) {
            $this->curlOption(CURLOPT_PORT, $this->_port);
        }

        $this->curlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);
        if ($this->sslVersion !== null) {
            $this->curlOption(CURLOPT_SSLVERSION, $this->sslVersion);
        }

        if (count($this->_curlUserOptions)) {
            foreach ($this->_curlUserOptions as $k => $v) {
                $this->curlOption($k, $v);
            }
        }

        $this->_headerCount = 0;
        $this->_responseHeaders = [];
        $this->_responseBody = curl_exec($this->_ch);

        $err = curl_errno($this->_ch);
        if ($err) {
            $this->doError(curl_error($this->_ch));
        }
        curl_close($this->_ch);
    }
}