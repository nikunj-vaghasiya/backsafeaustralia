<?php
namespace Digital\Ourteam\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class OurteamActions extends Column
{
    /** Url path */
    const TEAM_URL_PATH_EDIT = 'ourteam/team/edit';
    const TEAM_URL_PATH_DELETE = 'ourteam/team/delete';
    /** @var UrlInterface */

    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::TEAM_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $name = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['team_id'])) {
                continue;
            }

            $memberName = trim((string)($item['name'] ?? ''));
            if ($memberName === '') {
                $memberName = (string)__('this member');
            }

            $item[$name]['edit'] = [
                'href' => $this->urlBuilder->getUrl($this->editUrl, ['id' => $item['team_id']]),
                'label' => __('Edit'),
                '__disableTmpl' => true,
            ];

            $item[$name]['delete'] = [
                'href' => $this->urlBuilder->getUrl(
                    self::TEAM_URL_PATH_DELETE,
                    ['id' => $item['team_id']]
                ),
                'label' => __('Delete'),
                'confirm' => [
                    'title' => __('Delete "%1"', $memberName),
                    'message' => __('Are you sure you want to delete "%1"?', $memberName),
                    '__disableTmpl' => true,
                ],
                'post' => true,
                '__disableTmpl' => true,
            ];
        }

        return $dataSource;
    }
}