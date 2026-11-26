<?php

namespace Integration\Base\Model\Config\Source;

class AuthenticationMethod implements \Magento\Framework\Option\ArrayInterface
{
    const BASIC_AUTH = 'basic_auth';
    const LOGIN_AUTH = 'login_auth';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BASIC_AUTH, 'label' => __('Basic Authentication')],
            ['value' => self::LOGIN_AUTH, 'label' => __('Login Authentication')],
        ];
    }
}
