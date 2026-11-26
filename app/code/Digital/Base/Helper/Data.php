<?php
namespace Digital\Base\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /* @codingStandardsIgnoreStart */
    const FACEBOOK_LINK = 'digitalconfig/social_links/facebook_link';
    const YOUTUBE_LINK = 'digitalconfig/social_links/youtube_link';
    const INSTAGRAM_LINK = 'digitalconfig/social_links/instagram_link';
    const TWITTER_LINK = 'digitalconfig/social_links/twitter_link';
    const LINKEDIN_LINK = 'digitalconfig/social_links/linkedin_link';

    const FACEBOOK_ICON = 'digitalconfig/icons/svg_facebook';
    const INSTAGRAM_ICON = 'digitalconfig/icons/svg_instagram';
    const TWITTER_ICON = 'digitalconfig/icons/svg_twitter';
    const YOUTUBE_ICON = 'digitalconfig/icons/svg_youtube';
    const LINKEDIN_ICON = 'digitalconfig/icons/svg_linkedin';
    const BACKTOTOP_ICON = 'digitalconfig/icons/svg_backto_top';

    const XML_PATH_SECRETKEY = "digitalconfig/captchakey/recapcha_secretkey";
    const XML_PATH_SITEKEY = "digitalconfig/captchakey/recapcha_sitekey";

    /* @codingStandardsIgnoreEnd */

    /**
     * @var string
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_imageFactory;

    protected $_customerSession;

    protected $driverFile;

    protected $request;

    protected $fileDriver;

    protected $_storeInterface;
    
    protected $_scopeConfig;
    protected $filterProvider;


    /**
     * Base helper
     *
     * @param Context               $context
     * @param Session               $customerSession
     * @param File                  $driverFile
     * @param RequestInterface      $httpRequest
     * @param StoreManagerInterface $storeInterface
     * @param Filesystem            $filesystem
     * @param AdapterFactory        $imageFactory
     * @param File                  $fileDriver
     * @param ScopeConfigInterface  $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Magento\Store\Model\StoreManagerInterface $storeInterface,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->driverFile = $driverFile;
        $this->request = $httpRequest;
        $this->_storeInterface = $storeInterface;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->fileDriver = $fileDriver;
        $this->_scopeConfig = $scopeConfig;
        $this->filterProvider = $filterProvider;
    }

    /**
     * IsCustomerLogin
     *
     * @return void
     */
    public function isCustomerLogin()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * IsNonEnglishString
     *
     * @param string $string
     * @return bool
     */
    public function isNonEnglishString($string = '')
    {
        $flag = false;
        $string = trim($string);
        $badnameArr = ['drive.google.com', 'drive.google'];
        $badnameArrCounts = count($badnameArr);
        for ($x = 0; $x < $badnameArrCounts; $x++) {
            if (strpos($string, $badnameArr[$x]) !== false) {
                $flag = true;
            }
        }
        if ($flag == false) {
            $flag = (bool) (!preg_match("/^[A-Za-z0-9_! \"#$%&'~`|()\[\]{}<>*+,\-.\\:\/;=?@^_]+$/", $string));
        }
        return $flag;
    }

    /**
     * Get Badcharacter
     *
     * @param custDetail $custDetail
     * @return bool
     */
    public function getBadcharacter($custDetail)
    {
        $flag = false;

        /* Chinese validation start */
        $custDetailCounts = count($custDetail);
        for ($i = 0; $i < $custDetailCounts; $i++) {
            if (preg_match("/^\p{Han}+$/u", trim($custDetail[$i]))) {
                $flag = true;
                break;
            } else {
                $badnameArr = ['开发票', '验后付款'];
                $badnameArrCounts = count($badnameArr);
                for ($x = 0; $x < $badnameArrCounts; $x++) {
                    if ((strpos($custDetail[$i], $badnameArr[$x]) !== false)) {
                        $flag = true;
                        break;
                    }
                }
                $bademailArr = ['@qq', '@cnyongfeng', '@163.', '@169.', '@126.',
                    '@139.', '@sina', '@yahoo.com.cn', '@aliyun'];
                $bademailArrCounts = count($bademailArr);
                for ($x = 0; $x < $bademailArrCounts; $x++) {
                    if (strpos($custDetail[$i], $bademailArr[$x]) !== false) {
                        $flag = true;
                        break;
                    }
                }
            }
        }

        return $flag;
    }

    /**
     * Get Store Config Path
     */
    public function getStoreScope()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $storeScope;
    }

    /**
     * Get Captcha sitekey
     */
    public function getCaptchaSitekey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Captcha secretkey
     */
    public function getCaptchaSecretkey()
    {
        $storeScope = $this->getStoreScope();
        return $this->scopeConfig->getValue(self::XML_PATH_SECRETKEY, $storeScope);
    }

    /**
     * Function for check recaptcha and validation
     *
     * @param Post $post
     * @return void
     */
    public function validateRecaptcha($post)
    {
        $error = false;
        $requestMethod = $this->request->getServer('REQUEST_METHOD');
        if ($requestMethod === 'POST') {
            // Build POST request:
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = $this->getCaptchaSecretkey();
            $recaptcha_response = $post['recaptcha_response'];

            // Make and decode POST request:
            $recaptcha = $this->driverFile->fileGetContents(
                $recaptcha_url . '?secret=' .
                $recaptcha_secret . '&response=' .
                $recaptcha_response
            );
            $recaptcha = json_decode($recaptcha);

            // Take action based on the score returned:
            if ($recaptcha->success == null || $recaptcha->success == '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase('Google reCaptcha authentication timeout.')
                );
            }
            if ($recaptcha->success == 1 && $recaptcha->score < 0.5) {
                $error = true;
                return $error;
            }
        }
        if ($error) {
            throw new \Magento\Framework\Exception\LocalizedException('Please Enter Valid Data.');
        }
    }

    /**
     * CheckDevice
     *
     * @return void
     */
    public function checkDevice()
    {
        /* @codingStandardsIgnoreStart */
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        /* @codingStandardsIgnoreEnd */
        $iphone = strpos($userAgent, "iPhone");
        $android = strpos($userAgent, "Android");
        $mobile = strpos($userAgent, "Mobile");
        $palmpre = strpos($userAgent, "webOS");
        $berry = strpos($userAgent, "BlackBerry");
        $ipod = strpos($userAgent, "iPod");
        // Tablet
        $ipad = strpos(strtolower($userAgent), "ipad");
        $playbook = strpos(strtolower($userAgent), "playbook");
        $kindle = strpos(strtolower($userAgent), "kindle");
        $tablet = strpos(strtolower($userAgent), "tablet");

        if ($iphone || ($android && $mobile) || $palmpre || $ipod || $berry == true) {
            return 1;
        } elseif ($ipad || ($android && $tablet) || $kindle || $playbook == true) {
            return 2;
        }
        return 0;
    }

    /**
     * Pass imagename, width and height
     *
     * @param Post $image
     * @param Post $imageMediaFilePath
     * @param Post $width
     * @param Post $height
     * @return void
     */
    public function getResizedImage($image, $imageMediaFilePath = null, $width = null, $height = null)
    {
        $filesystemMedia = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $absolutePath = $filesystemMedia->getAbsolutePath($imageMediaFilePath) . '/' . $image;
        if (!$this->fileDriver->isExists($absolutePath)) {
            return false;
        }

        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath($imageMediaFilePath . '/resized/' . $width . '/') . $image;

        if (!$this->fileDriver->isExists($imageResized)) {
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(false);
            $imageResize->resize($width, $height);
            $destination = $imageResized;
            $imageResize->save($destination);
        }
        $resizedURL = $this->getMediaPath() . $imageMediaFilePath . '/resized/' . $width . '/' . $image;
        return $resizedURL;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue($config_path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Facebooklink
     *
     * @return void
     */
    public function getFacebookLink()
    {
        return $this->getConfig(self::FACEBOOK_LINK);
    }

    /**
     * Get Instagramlink
     *
     * @return void
     */
    public function getInstagramLink()
    {
        return $this->getConfig(self::INSTAGRAM_LINK);
    }

    /**
     * Get Twitterlink
     *
     * @return void
     */
    public function getTwitterLink()
    {
        return $this->getConfig(self::TWITTER_LINK);
    }

    /**
     * Get Youtubelink
     *
     * @return void
     */
    public function getYoutuberLink()
    {
        return $this->getConfig(self::YOUTUBE_LINK);
    }

    public function getLinkedInLink()
    {
        return $this->getConfig(self::LINKEDIN_LINK);
    }

    /**
     * Get Facebookicon
     *
     * @return void
     */
    public function getFacebookIcon()
    {
        return $this->getConfig(self::FACEBOOK_ICON);
    }

    /**
     * Get Instagramicon
     *
     * @return void
     */
    public function getInstagramIcon()
    {
        return $this->getConfig(self::INSTAGRAM_ICON);
    }

    /**
     * Get Twittericon
     *
     * @return void
     */
    public function getTwitterIcon()
    {
        return $this->getConfig(self::TWITTER_ICON);
    }

    /**
     * Get Youtubeicon
     *
     * @return void
     */
    public function getYoutubeIcon()
    {
        return $this->getConfig(self::YOUTUBE_ICON);
    }

    public function getLinkedInIcon()
    {
        return $this->getConfig(self::LINKEDIN_ICON);
    }

    /**
     * Get Backtotopicon
     *
     * @return void
     */
    public function getBacktotopIcon()
    {
        return $this->getConfig(self::BACKTOTOP_ICON);
    }

    /**
     * Get Store
     *
     * @return void
     */
    public function getStore()
    {
        return $this->_storeInterface->getStore();
    }

    /**
     * Get Store ID
     *
     * @return void
     */
    public function getStoreId()
    {
        return (int) $this->_storeInterface->getStore()->getId();
    }

    /**
     * Get MediaPath
     */
    public function getMediaPath()
    {
        $mediaUrl = $this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    /**
     * Get Base URL
     */
    public function getBaseUrl()
    {
        $mediaUrl = $this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        return $mediaUrl;
    }

    /**
     * Get Check URL
     *
     * @param URL $url
     */
    public function checkUrl($url = null)
    {
        // Cast $url to string to avoid passing null to trim()
        $url = (string) $url;

        if (!empty($url)
            && (strpos($url, "http://") === false
                && strpos($url, "https://") === false
                && strpos($url, "www") === false)
        ) {
            $url = trim($url);
            $url = ltrim($url, '/');
            return $this->getBaseUrl() . $url;
        }

        return trim($url);
    }

    public function getFilteredWysiwygContent($content)
    {
        return $this->filterProvider->getPageFilter()->filter($content);
    }

}
