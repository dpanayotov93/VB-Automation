<?php

$user = array("EN" => "User","BG" => "Потребител");
$oid = array("EN" => "OrderID","BG" => "OrderID");
$uid = array("EN" => "UserID","BG" => "UserID");
$payment = array("EN" => "Payment method","BG" => "Начин на плащане");
$date = array("EN" => "Date of order","BG" => "Дата на поръчка");
$ip = array("EN" => "Client IP address","BG" => "IP адрес на клиента");
$status = array("EN" => "Order status","BG" => "Статус на поръчката");
$address = array("EN" => "Reciever address","BG" => "Адрес за получаване");
$totalPrice = array("EN" => "Total price","BG" => "Обща стойност");
$product = array("EN" => "Product","BG" => "Продукт");
$pid = array("EN" => "ProductID","BG" => "ProductID");


$nameLang = array("EN" => "Product","BG" => "Продукт");
$priceLang = array("EN" => "Price","BG" => "Цена");

function getStatusOfOrder($o,$l) {
	$orderStatus = array();
	$orderStatus["BG"] = array("Неодобрена поръчка","Одобрена поръчка","Изпратена поръчка");
	$orderStatus["EN"] = array("Unreviewed order","Reviewed order","Sent order");
	return $orderStatus[$l][$o];
}

function getDeliveryOptionOfOrder($o,$l) {
	$orderDelivery = array();
	$orderDelivery["BG"] = array("Еконт");
	$orderDelivery["EN"] = array("Econt");
	return $orderDelivery[$l][$o];
}

function getPaymentMethodOfOrder($o,$l) {
	$orderPayment = array();
	$orderPayment["BG"] = array("Банков превод","Наложен платеж","PayPal");
	$orderPayment["EN"] = array("Bank transaction","Cash on delivery","PayPal");
	return $orderPayment[$l][$o];
}

?>