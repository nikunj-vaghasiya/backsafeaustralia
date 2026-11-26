<?php

namespace Integration\Base\Model\Config\Source;

class IntegrationType implements \Magento\Framework\Option\ArrayInterface
{
    const MYOB_EXO = 'myob-exo';
    const MYOB_ADVANCED = 'myob-advanced';
    const ATTACHE = 'attache';
    const WIISE = 'wiise';
    const SAP = 'sap';
    const AX = 'ax';
    const SAGE = 'sage';
    const HUBSPOT = 'hubspot';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MYOB_EXO, 'label' => __('MYOB Exo')],
            ['value' => self::MYOB_ADVANCED, 'label' => __('MYOB Advanced')],
            ['value' => self::ATTACHE, 'label' => __('Attache')],
            ['value' => self::WIISE, 'label' => __('WIISE')],
            ['value' => self::SAP, 'label' => __('SAP')],
            ['value' => self::AX, 'label' => __('AX')],
            ['value' => self::SAGE, 'label' => __('SAGE')],
            ['value' => self::HUBSPOT, 'label' => __('HUBSPOT')]
        ];
    }
}
