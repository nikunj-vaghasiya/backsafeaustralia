<?php

namespace Digital\Config\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /* @codingStandardsIgnoreStart */
    const NATIONWIDE_TEXT = 'digitalconfig/top_links/nationwide_text';
    const TOLLFREE_NUMBER = 'digitalconfig/top_links/tollfree_number';
    const OPENING_HOURS = 'digitalconfig/top_links/opening_hours';
    const OPENING_DAYS = 'digitalconfig/top_links/opening_days';
    const STORE_EMAIL = 'digitalconfig/footer_links/store_email';
    const NEWSLETTER_TEXT = 'digitalconfig/footer_links/newsletter_text';
    const FACEBOOK_LINK = 'digitalconfig/footer_links/facebook_link';
    const INSTAGRAM_LINK = 'digitalconfig/footer_links/instagram_link';
    const FOOTER_LOGO = 'digitalconfig/footer_links/footer_logo';
    const SVG_NATIONWIDE = 'digitalconfig/icons/svg_nationwide';
    const SVG_SEARCH = 'digitalconfig/icons/svg_search';
    const SVG_USER = 'digitalconfig/icons/svg_user';
    const SVG_CLOSE_MENU = 'digitalconfig/icons/svg_close_menu';
    const SVG_SUBSCRIBE = 'digitalconfig/icons/svg_subscribe';
    const SVG_FACEBOOK = 'digitalconfig/icons/svg_facebook';
    const SVG_INSTAGRAM = 'digitalconfig/icons/svg_instagram';
    const SVG_TWITTER = 'digitalconfig/icons/svg_twitter';
    const SVG_LINKEDIN = 'digitalconfig/icons/svg_linkedin';
    const SVG_EMAIL = 'digitalconfig/icons/svg_email';
    const SVG_SORT_ASC = 'digitalconfig/icons/svg_sort_asc';
    const SVG_SORT_DESC = 'digitalconfig/icons/svg_sort_desc';
    const SVG_VIEW_ORDER = 'digitalconfig/icons/svg_view_order';
    const SVG_RE_ORDER = 'digitalconfig/icons/svg_re_order';
    const SVG_BACKTOTOP = 'digitalconfig/icons/svg_backto_top';
    const SVG_LOCATION = 'digitalconfig/icons/svg_location';
    const SVG_TALK_TO_US = 'digitalconfig/icons/svg_talk_to_us';
    const SVG_CONTACT = 'digitalconfig/icons/svg_contact';
    const SVG_WISHLIST = 'digitalconfig/icons/svg_wishlist';
    const QUESTION_MARK_ICON = 'digitalconfig/icons/svg_question_icon';
    const CMSPAGE_BANNER = 'digitalconfig/view/cms_banner';
    const STORE_INFO_TEMPLATE = "{{var street_line1}}\n" .
        "{{depend street_line2}}{{var street_line2}}\n{{/depend}}" .
        "{{depend city}}{{var city}},{{/depend}}\n" . "{{var region}}, {{depend postcode}}{{var postcode}},{{/depend}} {{var country}}\n" .
        "{{depend hours}}{{var hours}}{{/depend}}";
    /* @codingStandardsIgnoreEnd */
    /**
     *
     * @var FilterManager
     */
    protected $filterManager;
    protected $_page;
    protected $_scopeConfig;
    protected $_storeInformation;
    protected $_storeInterface;

    /**
     *
     * @var string
     */
    private $template;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Information $storeInformation,
        \Magento\Store\Model\StoreManagerInterface $storeInterface,
        \Magento\Framework\Filter\FilterManager $filterManager,
        $template = self::STORE_INFO_TEMPLATE
    ) {
        parent::__construct($context);
        $this->_page = $page;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeInformation = $storeInformation;
        $this->_storeInterface = $storeInterface;
        $this->filterManager = $filterManager;
        $this->template = $template;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getStore()
    {
        return $this->_storeInterface->getStore();
    }

    /* Media Path*/
    public function getMediaPath()
    {
        $mediaUrl = $this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl;
    }
    /* Media Path*/

    /* SVG Icons */
    public function getNationwideSvgIcon()
    {
        return $this->getConfig(self::SVG_NATIONWIDE);
    }

    public function getSearchSvgIcon()
    {
        return $this->getConfig(self::SVG_SEARCH);
    }

    public function getUserSvgIcon()
    {
        return $this->getConfig(self::SVG_USER);
    }

    public function getMenuCloseSvgIcon()
    {
        return $this->getConfig(self::SVG_CLOSE_MENU);
    }

    public function getSubscribeSvgIcon()
    {
        return $this->getConfig(self::SVG_SUBSCRIBE);
    }

    public function getFacebookSvgIcon()
    {
        return $this->getConfig(self::SVG_FACEBOOK);
    }

    public function getInstagramSvgIcon()
    {
        return $this->getConfig(self::SVG_INSTAGRAM);
    }

    public function getTwitterSvgIcon()
    {
        return $this->getConfig(self::SVG_TWITTER);
    }

    public function getLinkedinSvgIcon()
    {
        return $this->getConfig(self::SVG_LINKEDIN);
    }

    public function getEmailSvgIcon()
    {
        return $this->getConfig(self::SVG_EMAIL);
    }

    public function getSortAscSvgIcon()
    {
        return $this->getConfig(self::SVG_SORT_ASC);
    }

    public function getSortDescSvgIcon()
    {
        return $this->getConfig(self::SVG_SORT_DESC);
    }

    public function getViewOrderSvgIcon()
    {
        return $this->getConfig(self::SVG_VIEW_ORDER);
    }

    public function getReOrderSvgIcon()
    {
        return $this->getConfig(self::SVG_RE_ORDER);
    }

    public function getBackToTopIcon()
    {
        return $this->getConfig(self::SVG_BACKTOTOP);
    }

    public function getLocationIcon()
    {
        return $this->getConfig(self::SVG_LOCATION);
    }

    public function getTalkToUsIcon()
    {
        return $this->getConfig(self::SVG_TALK_TO_US);
    }

    public function getContactIcon()
    {
        return $this->getConfig(self::SVG_CONTACT);
    }

    public function getWishlistIcon()
    {
        return $this->getConfig(self::SVG_WISHLIST);
    }

    public function getQuestionMarkIcon()
    {
        return $this->getConfig(self::QUESTION_MARK_ICON);
    }
    /* SVG Icons */

    public function getFaviconPath()
    {
        return $this->getMediaPath() . "favicon/" . $this->scopeConfig->getValue(
            'design/head/shortcut_icon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getCmsPageBanner()
    {
        return $this->getMediaPath() . 'wysiwyg/cms/' . $this->getConfig(self::CMSPAGE_BANNER);
    }

    public function getCmsPageTitle()
    {
        if ($this->_page->getId()) {
            return $pageTitle = $this->_page->getTitle();
        }
    }

    public function getShowBanner()
    {
        if ($this->_page->getId()) {
            return $showBanner = $this->_page->getShowBanner();
        }
    }

    public function getNationWideText()
    {
        return $this->getConfig(self::NATIONWIDE_TEXT);
    }

    public function getTollFreeNumber()
    {
        return $this->getConfig(self::TOLLFREE_NUMBER);
    }

    public function getOpeningHours()
    {
        return $this->getConfig(self::OPENING_HOURS);
    }

    public function getOpeningDays()
    {
        return $this->getConfig(self::OPENING_DAYS);
    }

    public function getStoreEmailAddress()
    {
        return $this->getConfig(self::STORE_EMAIL);
    }

    public function getNewsletterText()
    {
        return $this->getConfig(self::NEWSLETTER_TEXT);
    }

    public function getFacebookLink()
    {
        return $this->getConfig(self::FACEBOOK_LINK);
    }

    public function getInstagramLink()
    {
        return $this->getConfig(self::INSTAGRAM_LINK);
    }

    public function getFooterLogo()
    {
        $footerLogo = $this->getConfig(self::FOOTER_LOGO);
        if ($footerLogo) {
            return $this->getMediaPath() . 'logo/footer/' . $footerLogo;
        }

        return '';
    }

    public function getStoreInformation()
    {
        return $this->_storeInformation->getStoreInformationObject($this->getStore());
    }

    public function getFormattedAddress()
    {
        $address = '';
        $storeInfo = $this->getStoreInformation();

        $address = $this->filterManager->template(
            $this->template,
            ['variables' => $storeInfo->getData()]
        );

        if ($address) {
            $address = nl2br($address);
        }

        return $address;
    }
}
