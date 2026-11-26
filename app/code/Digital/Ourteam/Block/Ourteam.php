<?php
declare(strict_types=1);

namespace Digital\Ourteam\Block;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Model\ResourceModel\Ourteam\Collection;
use Digital\Ourteam\Model\ResourceModel\Ourteam\CollectionFactory as TeamCollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\DB\Sql\Expression;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

/**
 * Our Team frontend block (Hyva compatible - no jQuery / RequireJS dependency)
 */
class Ourteam extends Template implements IdentityInterface
{
    /**
     * @var TeamCollectionFactory
     */
    private $teamCollectionFactory;

    /**
     * @var Collection|null
     */
    private $teamCollection = null;

    private $urlInterface;

    /**
     * @param Template\Context      $context
     * @param TeamCollectionFactory $teamCollectionFactory
     * @param array                 $data
     */
    public function __construct(
        Template\Context $context,
        TeamCollectionFactory $teamCollectionFactory,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->teamCollectionFactory = $teamCollectionFactory;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Active team members, ordered by sort order then id
     *
     * @return Collection
     */

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $currentUrl = strtok(
            $this->urlInterface->getCurrentUrl(),
            '?'
        );

        $this->pageConfig->addRemotePageAsset(
            $currentUrl,
            'canonical',
            [
                'attributes' => [
                    'rel' => 'canonical'
                ]
            ]
        );

        return $this;
    }
    
    public function getTeamCollection(): Collection
    {
        if ($this->teamCollection === null) {
            /** @var Collection $collection */
            $collection = $this->teamCollectionFactory->create();
            $collection->addFieldToFilter(OurteamInterface::STATUS, 1);

            // sort_order is stored as a varchar, cast so 2 sorts before 10
            $collection->getSelect()
                ->order(new Expression('CAST(sort_order AS UNSIGNED) ASC'))
                ->order('team_id ASC');

            $limit = (int)$this->getData('limit');
            if ($limit > 0) {
                $collection->setPageSize($limit);
            }

            $this->teamCollection = $collection;
        }

        return $this->teamCollection;
    }

    /**
     * Heading rendered above the grid. Pass heading="" to hide it.
     *
     * @return string
     */
    public function getHeading(): string
    {
        $heading = $this->getData('heading');

        return $heading === null ? (string)__('Our Team') : (string)$heading;
    }

    /**
     * Optional intro copy under the heading
     *
     * @return string
     */
    public function getSubheading(): string
    {
        return (string)$this->getData('subheading');
    }

    /**
     * Placeholder used when a member has no image
     *
     * @return string
     */
    public function getPlaceholderImageUrl(): string
    {
        return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/image.jpg');
    }

    /**
     * Cache tags so the block is flushed when a team member is saved
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [OurteamInterface::CACHE_TAG];
    }
}
