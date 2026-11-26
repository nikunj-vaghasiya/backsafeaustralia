<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Digital\Productguide\ViewModel;

use Digital\Productguide\Helper\Data;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Provides the user data to fill the form.
 */
class UserDataProvider implements ArgumentInterface
{

    /**
     * @var Data
     */
    private $helper;

    /**
     * UserDataProvider constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getFirstname()
    {
        if($this->helper->getPostValue('firstname'))
        {
            return $this->helper->getPostValue('firstname');
        }else{
            return $this->helper->getFirstname();
        }
    }

    public function getLastname()
    {
        if($this->helper->getPostValue('lastname'))
        {
            return $this->helper->getPostValue('lastname');
        }else{
            return $this->helper->getLastname();
        }
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if($this->helper->getPostValue('email'))
        {
            return $this->helper->getPostValue('email');
        }else{
            return $this->helper->getUserEmail();
        }
    }

    /**
     * Get user phone
     *
     * @return string
     */
    public function getUserPhone()
    {
        return $this->helper->getPostValue('phone');
    }

    /**
     * Get user message
     *
     * @return string
     */
    public function getUserMessage()
    {
        return $this->helper->getPostValue('message');
    }
}
