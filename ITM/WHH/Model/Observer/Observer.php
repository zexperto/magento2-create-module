<?php
		
namespace ITM\WHH\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
				
class Observer implements ObserverInterface {
				
	public function execute(\Magento\Framework\Event\Observer $observer) {
		
		$event_name = $observer->getEvent ()->getName ();
		switch ($event_name) {
			
			case "sales_order_place_after" :{
				
				}
				break;
					
			
				
		}		
		return $this;
	}
}