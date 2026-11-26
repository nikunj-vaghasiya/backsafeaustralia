<?php
namespace Integration\ManualSync\Model;

class Status
{
    /**#@+
     * Status values
     */
    const SYNC_RUNNING_STATUS = 'running';
    const SYNC_PENDING_STATUS = 'pending';
    const SYNC_SUCCESS_STATUS = 'success';
    const SYNC_ERROR_STATUS = 'error';
    const SYNC_DORMANT_STATUS = 'dormant';


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::SYNC_RUNNING_STATUS => __('Running'),
            self::SYNC_PENDING_STATUS => __('Pending'),
            self::SYNC_SUCCESS_STATUS => __('Success'),
            self::SYNC_ERROR_STATUS => __('Error'),
            self::SYNC_DORMANT_STATUS => __('Dormant')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
