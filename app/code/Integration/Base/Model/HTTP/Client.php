<?php

namespace Integration\Base\Model\HTTP;

class Client
{
    const COOKIE_FOLDER = '/pub/integration';
    const COOKIE_FILE_NAME = '/myobcookieall.txt';
    const RATE_LIMIT_DELAY = 100000; // 0.1 second in microseconds

    protected $_curl;
    protected $_baseHelper;
    protected $_logger;

    public function __construct(
        \Integration\Base\Model\HTTP\Client\Curl $curl,
        \Integration\Base\Helper\AbstractHelper $baseHelper
    ) {
        $this->_curl = $curl;
        $this->_baseHelper = $baseHelper;
        $this->initLogger();
    }

    /**
     * Initialize logger
     */
    private function initLogger()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/hubspot_integration.log');
        $this->_logger = new \Zend_Log();
        $this->_logger->addWriter($writer);
    }

    /**
     * Main API request handler
     */
    public function apiGetRequest($uri)
    {
        $response = ['status' => '', 'count' => 0, 'object' => []];

        try {
            if (!filter_var($uri, FILTER_VALIDATE_URL)) {
                throw new \Exception('Request url is not valid.');
            }

            $this->setupCurlHeaders();
            $filterOptions = $this->_baseHelper->getFilterOptions();
            $this->logRunContext($filterOptions);

            if ($filterOptions == 'no') {
                $object = $this->fetchWithoutFilter($uri);
                $response = $this->buildResponse($object, 200);
            } elseif ($filterOptions == 'current-previous') {
                $response = $this->fetchWithDateFilter();
            } else {
                $response = $this->fetchWithSpecificDate();
            }
        } catch (\Exception $e) {
            $this->_logger->err('API Request Error: ' . $e->getMessage());
            $response = ['status' => 500, 'count' => 0, 'object' => ['message' => $e->getMessage()]];
        }

        return $response;
    }

    /**
     * Setup common CURL headers
     */
    private function setupCurlHeaders()
    {
        $token = 'Bearer ' . $this->_baseHelper->getHubspotToken();

        $this->getCurlClient()->addHeader("Content-Type", "application/json");
        $this->getCurlClient()->addHeader("Accept", "application/json");
        $this->getCurlClient()->addHeader("Authorization", $token);

        $this->getCurlClient()->setOptions([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        ]);
    }

    /**
     * Fetch all products without date filter using pagination
     */
    private function fetchWithoutFilter($uri)
    {
        $pageSize = $this->getPageSize();
        $properties = $this->getProductProperties();
        $allResults = [];
        $after = null;

        $this->_logger->info('Fetching without filter - Page Size: ' . $pageSize);

        do {
            $apiEndpoint = $uri . '?limit=' . $pageSize . '&properties=' . $properties;
            $this->_logger->err('HTTP apiEndpoint: ' . $apiEndpoint);
            if ($after !== null) {
                $apiEndpoint .= '&after=' . $after;
            }
            $this->_logger->err('HTTP after apiEndpoint: ' . $apiEndpoint);

            $this->getCurlClient()->get($apiEndpoint);
            $response = json_decode($this->getCurlClient()->getBody(), true);
            $this->_logger->err(json_encode($response));
            $httpCode = $this->getCurlClient()->getStatus();

            if ($httpCode !== 200) {
                $this->_logger->err('HTTP Error: ' . $httpCode);
                break;
            }

            if (!is_array($response)) {
                break;
            }

            // HubSpot API response structure
            if (isset($response['results']) && is_array($response['results'])) {
                $allResults = array_merge($allResults, $response['results']);
                $after = $response['paging']['next']['after'] ?? null;
                
                $this->_logger->info('Fetched ' . count($response['results']) . ' records. Total: ' . count($allResults));
            } else {
                // Fallback for different API response structure
                $normalized = $this->normalizeResponse($response);
                $allResults = array_merge($allResults, $normalized);
                break;
            }

            // Rate limiting
            if ($after !== null) {
                usleep(self::RATE_LIMIT_DELAY);
            }

        } while ($after !== null);

        $this->_logger->info('Total records fetched: ' . count($allResults));
        return $allResults;
    }

    /**
     * Fetch products with current/previous date filter
     */
    private function fetchWithDateFilter()
    {
        $filterDate = $this->_baseHelper->getFilterDate();
        $date = ($filterDate == 'previous-date') ? $this->getPreviousDate() : $this->getCurrentDate();

        $this->_logger->info('Fetching with date filter: ' . $date);
        return $this->fetchProductsByDate($date);
    }

    /**
     * Fetch products with specific date filter
     */
    private function fetchWithSpecificDate()
    {
        $specificDate = $this->_baseHelper->getFilterSpecificDate();

        if (empty($specificDate)) {
            $this->_logger->err('Specific date is empty in config');
            return [
                'status' => 500,
                'count' => 0,
                'object' => ['message' => "Specific date is empty in config"]
            ];
        }

        $this->_logger->info('Fetching with specific date: ' . $specificDate);
        return $this->fetchProductsByDate($specificDate);
    }

    /**
     * Fetch products modified on a specific date with pagination
     */
    private function fetchProductsByDate($date)
    {
        $token = $this->_baseHelper->getHubspotToken();
        $allProducts = [];
        $after = 0;
        $limit = $this->getPageSize();
        $hasMore = true;

        $this->_logger->info('Fetching products for date: ' . $date . ' | Limit: ' . $limit);

        $start = new \DateTime($date . ' 00:00:00', new \DateTimeZone('Australia/Perth'));
        $start->setTimezone(new \DateTimeZone('UTC'));

        $end = new \DateTime($date . ' 23:59:59', new \DateTimeZone('Australia/Perth'));
        $end->setTimezone(new \DateTimeZone('UTC'));
    
        $value = $start->format('Y-m-d\TH:i:s.000\Z');
        $highValue = $end->format('Y-m-d\TH:i:s.999\Z');

        $this->_logger->info("UTC value:\n" . $value);
        $this->_logger->info("UTC highValue:\n" . $highValue);
        $perth = new \DateTimeZone('Australia/Perth');
        $this->_logger->info('Date used: ' . $date);
        $this->_logger->info('Window in Perth time: '
            . $start->setTimezone($perth)->format('Y-m-d H:i:s')
            . ' -> '
            . $end->setTimezone($perth)->format('Y-m-d H:i:s'));

        while ($hasMore) {
            $postData = [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'propertyName' => 'hs_lastmodifieddate',
                                'operator' => 'BETWEEN',
                                'value' => $value,
                                'highValue' => $highValue
                            ]
                        ]
                    ]
                ],
                'properties' => $this->getProductPropertiesArray(),
                'limit' => $limit,
                'after' => (string)$after
            ];

            $this->_logger->info("POST DATA:\n" . json_encode($postData, JSON_PRETTY_PRINT));

            $result = $this->makeHubspotSearchRequest($postData, $token);

            if ($result === null) {
                $hasMore = false;
                break;
            }

            if (isset($result['results'])) {
                $allProducts = array_merge($allProducts, $result['results']);
                $this->_logger->info('Fetched ' . count($result['results']) . ' records. Total: ' . count($allProducts));

                // Check for next page
                if (isset($result['paging']['next']['after'])) {
                    $after = $result['paging']['next']['after'];
                    usleep(self::RATE_LIMIT_DELAY);
                } else {
                    $hasMore = false;
                }
            } else {
                $hasMore = false;
            }
        }

        $this->_logger->info('Total products fetched: ' . count($allProducts));

        return $this->buildResponse($allProducts, 200);
    }

    /**
     * Make HubSpot search API request
     */
    private function makeHubspotSearchRequest($postData, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/products/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            $this->_logger->err('cURL Error: ' . $curlError);
            return null;
        }

        if ($httpCode !== 200) {
            $this->_logger->err('HTTP Error: ' . $httpCode . ' | Response: ' . $response);
            return null;
        }

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] === 'error') {
            $this->_logger->err('API Error: ' . ($result['message'] ?? 'Unknown error'));
            return null;
        }

        return $result;
    }

    /**
     * Get product properties as comma-separated string
     */
    private function getProductProperties()
    {
        return 'name,hs_sku,price,hs_lastmodifieddate,description,status,stock_group,supplier,weight,bc_inventory,hs_status';
    }

    /**
     * Get product properties as array
     */
    private function getProductPropertiesArray()
    {
        return [
            'name',
            'hs_sku',
            'hs_status',
            'price',
            'bc_inventory',
            'hs_lastmodifieddate',
            'description',
            'status',
            'stock_group',
            'supplier',
            'weight'
        ];
    }

    /**
     * Normalize API response to ensure consistent array format
     */
    private function normalizeResponse($object)
    {
        if ($object === null) {
            return [];
        }

        // Convert single object to array format
        if (is_array($object) && !empty($object) && !isset($object[0])) {
            return [$object];
        }

        return $object;
    }

    /**
     * Build standardized response array
     */
    private function buildResponse($object, $httpCode)
    {
        return [
            'status' => $httpCode,
            'count' => is_array($object) ? count($object) : 0,
            'object' => $object
        ];
    }

    /**
     * Get page size based on configuration
     */
    private function getPageSize()
    {
        $defaultPageSize = 100;

        if ($this->_baseHelper->getPaginationPageEnable() == 1) {
            return $this->_baseHelper->getPageSize();
        }

        return $defaultPageSize;
    }

    /**
     * Get CURL client instance
     */
    protected function getCurlClient()
    {
        return $this->_curl;
    }

    /**
     * Validate credentials
     */
    public function validateCredentials()
    {
        /* TO DO: Implement credential validation */
    }

    /**
     * Get cookie file directory path
     */
    public function getCookieFileDir()
    {
        return BP . self::COOKIE_FOLDER . self::COOKIE_FILE_NAME;
    }

    /**
     * Get previous date in Australia/Melbourne timezone
     */
    public function getPreviousDate()
    {
        return $this->getDateInTimezone('-1 days');
    }

    /**
     * Get current date in Australia/Melbourne timezone
     */
    public function getCurrentDate()
    {
        return $this->getDateInTimezone();
    }

    /**
     * Get date in specific timezone
     */
    private function getDateInTimezone($modifier = null)
    {
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Perth');
        
        $date = $modifier ? date('Y-m-d', strtotime($modifier)) : date('Y-m-d');
        
        date_default_timezone_set($currentTimezone);
        return $date;
    }


    private function logRunContext($filterOptions)
    {
        $nowUtc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $this->_logger->info('===== HUBSPOT SYNC RUN CONTEXT =====');
        $this->_logger->info('Filter By Date (config): ' . var_export($filterOptions, true));
        $this->_logger->info('Sync Date (config): ' . var_export($this->_baseHelper->getFilterDate(), true));
        $this->_logger->info('Specific Date (config): ' . var_export($this->_baseHelper->getFilterSpecificDate(), true));
        $this->_logger->info('PHP default timezone: ' . date_default_timezone_get());
        $this->_logger->info('Now UTC: ' . $nowUtc->format('Y-m-d H:i:s T'));
        $this->_logger->info('Now Perth: ' . $nowUtc->setTimezone(new \DateTimeZone('Australia/Perth'))->format('Y-m-d H:i:s T'));
        $this->_logger->info('Now Melbourne: ' . $nowUtc->setTimezone(new \DateTimeZone('Australia/Melbourne'))->format('Y-m-d H:i:s T'));
        $this->_logger->info('getCurrentDate() picks: ' . $this->getCurrentDate());
        $this->_logger->info('getPreviousDate() picks: ' . $this->getPreviousDate());
    }
}