<?php

$propNamesDef = array("catid","price","qty","imgurl","promo");
$propNamesDefldtemp = array("names","desc");
$propNamesDefldtempLang = array();

$propNamesLang = array();
for ($i = 0; $i < count($propNamesDef);$i++ )
	$propNamesLang[$propNamesDef[$i]] = array();

for ($i = 0; $i < count($propNamesDefldtemp);$i++ ) {
	$propNamesLang[$propNamesDefldtemp[$i]] = array();
	$propNamesDefldtempLang[$i] = $propNamesDefldtemp[$i].$language;
}

$propNamesLang[$propNamesDef[3]]["EN"] = "Image";
$propNamesLang[$propNamesDefldtempLang[0]]["EN"] = "Name";
$propNamesLang[$propNamesDef[1]]["EN"] = "Price";
$propNamesLang[$propNamesDef[2]]["EN"] = "Quantity";

$propNamesLang[$propNamesDefldtempLang[1]]["EN"] = "Description";



$propNamesLang[$propNamesDef[3]]["BG"] = "Снимка";
$propNamesLang[$propNamesDefldtempLang[0]]["BG"] = "Име";
$propNamesLang[$propNamesDef[1]]["BG"] = "Цена";
$propNamesLang[$propNamesDef[2]]["BG"] = "Количество";

$propNamesLang[$propNamesDefldtempLang[1]]["BG"] = "Описание";



$favoriteProductLang = array();
$favoriteProductLang["EN"] = "Favorite";
$favoriteProductLang["BG"] = "Любим";
$priceProductLang["EN"] = "Price";
$priceProductLang["BG"] = "Цена";


?>