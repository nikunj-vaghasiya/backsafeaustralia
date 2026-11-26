<?php

namespace Integration\Product\Model\Product;

class FieldMapping
{
	/**
     * return field mapping 1) Custom table fields 2) API fields
     * @return array
     */

	public function get()
	{
		return [
			'sku' => 'InventoryID/value',
			'status' => 'ItemStatus/value',
			'name'  => 'Description/value',
			'price'	=> 'DefaultPrice/value',
			'supplier_code'	=> 'VendorDetails/0/VendorID/value',
			'supplier_inventory_id'	=> 'VendorDetails/0/SupplierInventoryID/value',
			'warehouse' => 'DefaultWarehouseID/value',
			'volume' => 'DimensionVolume/value',
			/*'weight' => 'DimensionWeight/value',*/
			'weight' => 'DimensionVolume/value',
			'conversation_factor' => 'UOMConversions/0/ConversionFactor/value',
			'from_uom' => 'UOMConversions/0/FromUOM/value',
			'multiple_or_divide' => 'UOMConversions/0/MultiplyOrDivide/value',
			'to_uom' => 'UOMConversions/0/ToUOM/value',
			'base_uom' => 'BaseUOM/value',
			'sales_uom' => 'SalesUOM/value',
			'purchase_uom' => 'PurchaseUOM/value',
			'tax_category' => 'TaxCategory/value',
			'attributes' => 'Attributes'
		];
	}

}

?>
