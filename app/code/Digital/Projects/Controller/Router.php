<?php
namespace Digital\Projects\Controller;

use Digital\Projects\Model\ResourceModel\Projects\Collection as ProjectsCollection;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Digital\Base\Helper\Data;
use Psr\Log\LoggerInterface;

class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
    protected $_eventManager;
    protected $_storeManager;
    protected $_response;
    protected $baseHelper;
    protected $projectsCollection;
    protected $_logger;

    const ALL_STORE_ID = 0;

    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        ResponseInterface $response,
        Data $baseHelper,
        ProjectsCollection $projectsCollection,
        LoggerInterface $logger
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->baseHelper = $baseHelper;
        $this->projectsCollection = $projectsCollection;
        $this->_logger = $logger;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function match(RequestInterface $request)
    {
        //  $_identifier = trim($request->getPathInfo(), '/');

        // $character = '/';

        // $offset = strpos($_identifier, $character);

        // $_identifier = substr($_identifier, $offset+1);

        $path = trim($request->getPathInfo(), '/');

        $parts = explode('/', $path);
        
        // Redirect /Projects -> /projects
        if (!empty($parts[0]) && strcasecmp($parts[0], 'projects') === 0 && $parts[0] !== 'projects') {
            $parts[0] = 'projects';

            $this->_response->setRedirect('/' . implode('/', $parts), 301);
            $request->setDispatched(true);

            return $this->actionFactory->create(
                \Magento\Framework\App\Action\Redirect::class,
                ['request' => $request]
            );
        }

        // Split URL

        // Only handle project detail pages
        if (count($parts) < 2 || strtolower($parts[0]) !== 'projects') {
            return null;
        }

        // project slug
        $_identifier = $parts[1];


        $this->_logger->info('Matching identifier: ' . $_identifier);
        
        $collection = $this->projectsCollection->addFieldToFilter('projects_url', $_identifier)
            ->addFieldToFilter('status', 1)
            ->load();

        if ($collection->getSize()) {
            foreach ($collection->getData() as $data) {
                $request->setRouteName('projects');
                $request->setControllerName('view');
                $request->setActionName('index');
                $request->setParam('id', $data['projects_id']);

                $this->_logger->info('Setting route, controller, action, and ID params.');

                $condition = new DataObject(
                    [
                        'identifier' => $_identifier,
                        'request' => $request,
                        'continue' => true,
                    ]
                );

                $this->_eventManager->dispatch(
                    'digital_projects_controller_router_match_before',
                    ['router' => $this, 'condition' => $condition]
                );

                if ($condition->getRedirectUrl()) {
                    $this->_logger->info('Redirect URL found: ' . $condition->getRedirectUrl());
                    $this->_response->setRedirect($condition->getRedirectUrl());
                    $request->setDispatched(true);
                    return $this->actionFactory->create(
                        \Magento\Framework\App\Action\Redirect::class,
                        ['request' => $request]
                    );
                }

                if (!$condition->getContinue()) {
                    $this->_logger->info('Condition did not continue.');
                    return null;
                }

                if (!$request->getModuleName()) {
                    $this->_logger->info('No module name set in request.');
                    return null;
                }

                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);
                $this->_logger->info('Alias set for request.');

                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Forward::class,
                    ['request' => $request]
                );
            }
        }

        $this->_logger->info('No matching projects found.');
        return null;
    }
}
