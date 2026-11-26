<?php

/**
 * Ecomteck
 * Copyright (C) 2018 Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category Ecomteck
 * @package Ecomteck_ProductAttachment
 * @copyright Copyright (c) 2018 Ecomteck
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Ecomteck
 */

namespace Ecomteck\ProductAttachment\Controller\Adminhtml\Index;

use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class PostDataProcessor
{
    /**
     * @var Date
     */
    private $dateFilter;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
    }

    /**
     * Filter posted data (apply date filter if exists)
     */
    public function filter(array $data): array
    {
        if (isset($data['published_at']) && !empty($data['published_at'])) {
            try {
                $data['published_at'] = $this->dateFilter->filter($data['published_at']);
            } catch (\Exception $e) {
                // Optional: show admin error message
                $this->messageManager->addErrorMessage(__('Invalid date format for "Published At".'));
                $data['published_at'] = null;
            }
        }
        return $data;
    }

    public function validate(): bool
    {
        // Implement your own validation logic if needed
        return true;
    }
}
