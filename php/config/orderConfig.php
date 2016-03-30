<?php

include 'variables/orderVariables.php';

$data = array("status" => getStatusOfOrder(0,$language,true), "delivery" => getDeliveryOptionOfOrder(0, $language, true), "paymentMethod" => getPaymentMethodOfOrder(0, $language, true));
$statusMessage = makeStatusMessage(27, "success");

function getStatusOfOrder($o,$l,$showAll=null) {
	include 'variables/orderStatusVariables.php';
	if(isset($showAll))
		return $orderStatus[$l];
		
	if (isset($orderStatus[$l][$o]))
		return $orderStatus[$l][$o];
	else 
		return $orderStatus["EN"][0];
}

function getDeliveryOptionOfOrder($o,$l,$showAll=null) {
	include 'variables/orderDeliveryVariables.php';
	if (isset($showAll))
		return $orderDelivery[$l];
	
	if (isset($orderDelivery[$l][$o]))
		return $orderDelivery[$l][$o];
	else 
		return $orderDelivery["EN"][0];
}

function getPaymentMethodOfOrder($o,$l,$showAll=null) {
	include 'variables/orderPaymentMethodVariables.php';
	if (isset($showAll))
		return $orderPayment[$l];
	
	if (isset($orderPayment[$l][$o]))
		return $orderPayment[$l][$o];
	else 
		return $orderPayment["EN"][0];
}

?>