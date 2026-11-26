<?php
exit('Wrong time!');

include('object_class.php');

$logDir = 'customer/changeemail/';
try {
	$customerCollectionFactory = $GLOBALS['objectManager']->get('\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory')->create();

	$customerCollection = $customerCollectionFactory
								->addAttributeToSelect('*')
								->addAttributeToFilter("account_number", ['neq' => ''])
								->addAttributeToFilter("email", ['neq' => '']);
								/*->addAttributeToFilter("email", ['nin' => ['C000001-test1-cindy.bi@activekidsgroup.com.au','===test1-cindy.bi@activekidsgroup.com.au']])
								->addAttributeToFilter("email", ['in' => ['C000551-test1-ferntreegully@littlecharlies.com.au','C000163-test1-info@canterburychildcare.com.au','test8.23digital@gmail.com']])
								->setPageSize(10)
								->setCurPage(1)*/;

	if($customerCollection && $customerCollection->count()) {
		$specialChar = ['@', ' ', '  '];
		foreach($customerCollection as $customer) {
			$customEmail = '';
			$originalEmail = trim($customer->getEmail());
			$accountNumber = trim($customer->getAccountNumber());

			$GLOBALS['manualSyncHelper']->createLogFiles("Customer ID: " . $customer->getId() . " - Original Email: " . $originalEmail . " - Account Number: " . $accountNumber, 'INFO', $logDir , 'success.log');

			/* concate email id with account number */
			if ($originalEmail && $accountNumber) {
				$validCode = (strposArr($accountNumber, $specialChar) !== false) ? str_replace($specialChar, '-', $accountNumber) : $accountNumber;
				$customEmail = $validCode . "-" . $originalEmail;
			}

			$GLOBALS['manualSyncHelper']->createLogFiles("New Custom Email: " . $customEmail, 'INFO', $logDir , 'success.log');

			if(!empty($originalEmail) && !empty($customEmail) && empty($customer->getApiEmail())) {
				$customer->setEmail($customEmail);
				$customer->setApiEmail($originalEmail);

				try {
					/*$customer->save();*/
					$GLOBALS['manualSyncHelper']->createLogFiles("Email updated successfully", 'INFO', $logDir , 'success.log');
				} catch(\Exception $e) {
					$GLOBALS['manualSyncHelper']->createLogFiles("Error occurred during save. Please check error.log", 'INFO', $logDir , 'success.log');
					$GLOBALS['manualSyncHelper']->createLogFiles('Composite ID save customer error: ' . $e->getMessage(), 'CRIT', $logDir , 'error.log');
					$GLOBALS['manualSyncHelper']->createLogFiles("Additional Info => Customer ID: " . $customer->getId() . " - Original Email: " . $originalEmail . " - Account Number: " . $accountNumber . " - New custom email: " . $customEmail, 'CRIT', $logDir , 'error.log');
				}
			} else {
				$GLOBALS['manualSyncHelper']->createLogFiles("Email not updated because API email already exits or Custom email is not valid - API Email: " . $customer->getApiEmail(), 'INFO', $logDir , 'success.log');
			}
		}
	}

	echo "Finished Successfully";
} catch(\Exception $e) {
	$GLOBALS['manualSyncHelper']->createLogFiles('Composite ID update customer error: '.$e->getMessage(), 'CRIT', $logDir , 'error.log');
}

function strposArr($haystack, $needles)
{
	if (!is_array($needles)) {
		$needles = array($needles);
		foreach ($needles as $needle) {
			if (($pos = strpos($haystack, $needle)) !== false) {
				return $pos;
			}
		}
	}
	return false;
}
?>