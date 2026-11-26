<?php
namespace Digital\Productenquiry\Model\Export;

use Magento\Ui\Model\Export\ConvertToCsv as DefauleConvertToCsv;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class ConvertToCsv extends DefauleConvertToCsv
{
    /**
    * @var SerializerInterface
    */
    protected $serializer;

    /**
    * @var EncryptorInterface
    */
    protected $encryptor;
    
    public function __construct(
    SerializerInterface $serializer,
    EncryptorInterface $encryptor
    ) {
    $this->serializer = $serializer;
    $this->encryptor = $encryptor;
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        /*$name = md5(microtime());*/
        $name = $this->encryptor->hash(microtime(), EncryptorInterface::HASH_VERSION_SHA256);
        $file = 'export/'. $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int) $dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
                
            foreach ($items as $item) {
                
                if ($component->getName()=='digital_productenquiry_listing') {
                
                    if (isset($item['products'])) {
                    
                        $value = $item['products'];
                        $individualHtml = "";
                        if ($value!='') {
                            $productsData = @unserialize($value);
                            /*$productsData = $this->serializer->unserialize($value);*/
                            if ($productsData !== false || $value === 'b:0;') {
                                foreach ($productsData as $productdata) {
                                    if (isset($productdata['sku'])) {
                                        $individualHtml .= $productdata['name']." [".$productdata['sku']."] "." (".$productdata['qty'].")";
                                    } else {
                                        $individualHtml .= $productdata['name']." (".$productdata['qty'].")";
                                    }
                                }
                            }
                            $item['products'] = $individualHtml;
                        }
                    }
                }
                
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        /*$total = $dataProvider->getTotalRow($searchCriteria);*/
        $writeTotal = [];
        foreach ($fields as $column) {
            $writeTotal[] = ''; /*$total[$column];*/
        }
        $stream->writeCsv($writeTotal);

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
