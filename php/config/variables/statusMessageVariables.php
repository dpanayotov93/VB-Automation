<?php
// ----------ERROR---------- //
$errorMsg = array();
// ------------EN----------- //
//GENERAL ERRORS
$errorMsg["EN"][0] = "Empty query request.";
$errorMsg["EN"][1] = "Could not connect to database.";
$errorMsg["EN"][2] = "Could not get language information.";
$errorMsg["EN"][3] = "Permision denied.";
$errorMsg["EN"][4] = "Incomplete query request.";
$errorMsg["EN"][5] = "Database error.";
//USER LOGIN ERRORS
$errorMsg["EN"][10] = "Not logged in.";
$errorMsg["EN"][11] = "Invalid user or password.";
$errorMsg["EN"][12] = "Error while loging in.";
$errorMsg["EN"][13] = "This user is already registered.";
$errorMsg["EN"][14] = "Already logged in.";
$errorMsg["EN"][15] = "Too many failed attempts. Try again later.";
$errorMsg["EN"][16] = "Multiple results for this user.";
//GETTING DATA
$errorMsg["EN"][50] = "No users to show";
$errorMsg["EN"][51] = "No categories found.";
$errorMsg["EN"][52] = "No products found.";
$errorMsg["EN"][53] = "No properties found.";
$errorMsg["EN"][54] = "No delivery options found.";
$errorMsg["EN"][55] = "No favorites found.";
$errorMsg["EN"][56] = "No discounts found.";
$errorMsg["EN"][57] = "No orders found.";
$errorMsg["EN"][58] = "";
$errorMsg["EN"][59] = "Nothing to select.";
//OTHER ERRORS
$errorMsg["EN"][101] = "Already logged in as different user.";
$errorMsg["EN"][102] = "Propery with that name already exist.";
$errorMsg["EN"][103] = "Could not assign properties to category.";
$errorMsg["EN"][104] = "Discount already exists for that user and category.";
$errorMsg["EN"][105] = "Discount already exists for that user and product.";
// ------------EN----------- //

// ------------BG----------- //
//GENERAL ERRORS
$errorMsg["BG"][0] = "Празна заявка.";
$errorMsg["BG"][1] = "Грешка при връзката с базата данни.";
$errorMsg["BG"][2] = "Възникна грешка при избиране на език.";
$errorMsg["BG"][3] = "Достъпът отказан.";
$errorMsg["BG"][4] = "Непълна заявка.";
$errorMsg["BG"][5] = "Грешка в базата данни.";
//USER LOGIN ERRORS
$errorMsg["BG"][10] = "Трябва да сте влезли в ситемата.";
$errorMsg["BG"][11] = "Невалиден потребител или парола.";
$errorMsg["BG"][12] = "Грешка при влизането в системата.";
$errorMsg["BG"][13] = "Това потребителско име вече е заето.";
$errorMsg["BG"][14] = "Вече сте влезли в системата.";
$errorMsg["BG"][15] = "Прекалено много невалидни опити за вход. Опитайте по-късно.";
$errorMsg["BG"][16] = "Същеструват повече от един потребител с това име.";
//GETTING DATA
$errorMsg["BG"][50] = "Няма намерени потребители.";
$errorMsg["BG"][51] = "Няма намерени категории.";
$errorMsg["BG"][52] = "Няма намерени продукти.";
$errorMsg["BG"][53] = "Няма намерени ствойства.";
$errorMsg["BG"][54] = "Няма намерени данни за доставка.";
$errorMsg["BG"][55] = "Няма намерени любими елементи.";
$errorMsg["BG"][56] = "Няма намерени отстъпки.";
$errorMsg["BG"][57] = "Няма намерени поръчки.";
$errorMsg["EN"][58] = "";
$errorMsg["BG"][59] = "Нищо не е намерено.";
//OTHER ERRORS
$errorMsg["BG"][101] = "Already logged in as different user.";
$errorMsg["BG"][102] = "Propery with that name already exist.";
$errorMsg["BG"][103] = "Could not assign properties to category.";
$errorMsg["BG"][104] = "Discount already exists for that user and category.";
$errorMsg["BG"][105] = "Discount already exists for that user and product.";
// ------------BG----------- //
// ----------ERROR---------- //

// ---------SUCCESS--------- //
$succesMsg = array();
// ------------EN----------- //
//LOGIN
$succesMsg["EN"][1] = "Successful login!";
$succesMsg["EN"][1] = "Successful logout!";
//ADDING DATA
$succesMsg["EN"][10] = "User information successfully added.";
$succesMsg["EN"][11] = "Category successfully added.";
$succesMsg["EN"][12] = "Product succesfully added.";
$succesMsg["EN"][13] = "Property successfully added.";
$succesMsg["EN"][14] = "Delivery information added.";
$succesMsg["EN"][15] = "Favorite saved.";
$succesMsg["EN"][16] = "Discount information added.";
$succesMsg["EN"][17] = "Order successfully added.";
//GETTING DATA
$succesMsg["EN"][20] = "User information sent.";
$succesMsg["EN"][21] = "Category information sent.";
$succesMsg["EN"][22] = "Products information sent.";
$succesMsg["EN"][23] = "Property information sent.";
$succesMsg["EN"][24] = "Delivery information sent.";
$succesMsg["EN"][25] = "Favorites information sent.";
$succesMsg["EN"][26] = "Discount information sent";
$succesMsg["EN"][27] = "Order information sent.";
$succesMsg["EN"][28] = "Search information sent.";
$succesMsg["EN"][29] = "Language information sent.";
//UPDATE DATA
$succesMsg["EN"][30] = "User information successfully updated.";
$succesMsg["EN"][31] = "Category successfully updated.";
$succesMsg["EN"][32] = "Product successfully updated.";
$succesMsg["EN"][33] = "Property successfully updated.";
$succesMsg["EN"][34] = "Delivery information successfully updated.";
$succesMsg["EN"][35] = ""; //
$succesMsg["EN"][36] = "Discount information successfully updated.";
$succesMsg["EN"][37] = "Order successfully updated.";
$succesMsg["EN"][39] = "Language information successfully updated.";
//DELETE DATA
$succesMsg["EN"][40] = "User successfully deleted.";
$succesMsg["EN"][41] = "Category successfully deleted.";
$succesMsg["EN"][42] = "Product successfully deleted.";
$succesMsg["EN"][43] = "Property successfully deleted.";
$succesMsg["EN"][44] = "Delivery information successfully deleted.";
$succesMsg["EN"][45] = "Favorite item successfully deleted.";
$succesMsg["EN"][46] = "Discount information successfully deleted.";
// ------------EN----------- //

// ------------BG----------- //
//LOGIN
$succesMsg["BG"][1] = "Успешен вход.";
//ADDING DATA
$succesMsg["BG"][10] = "Потребителската информация беше добавена успешно.";
$succesMsg["BG"][11] = "Категорията беше добавена успешно.";
$succesMsg["BG"][12] = "Продуктът беше добавен успешно.";
$succesMsg["BG"][13] = "Свойството беше добавено успешн.";
$succesMsg["BG"][14] = "Информацията за доставка беше добавена успешно.";
$succesMsg["BG"][15] = "Записано като любим продукт.";
$succesMsg["BG"][16] = "Информация за отстъпки беше добавена.";
$succesMsg["BG"][17] = "Поръчката беше успесно записана.";
//GETTING DATA
$succesMsg["BG"][20] = "User information sent.";
$succesMsg["BG"][21] = "Category information sent.";
$succesMsg["BG"][22] = "Products information sent.";
$succesMsg["BG"][23] = "Property information sent.";
$succesMsg["BG"][24] = "Delivery information sent.";
$succesMsg["BG"][25] = "Favorites information sent.";
$succesMsg["BG"][26] = "Discount information sent";
$succesMsg["BG"][27] = "Order information sent.";
$succesMsg["BG"][28] = "Search information sent.";
$succesMsg["BG"][29] = "Language information sent.";
//UPDATE DATA
$succesMsg["BG"][30] = "Протребителската информация беше успешно обновена.";
$succesMsg["BG"][31] = "Категорията беше успешно обновена.";
$succesMsg["BG"][32] = "Продуктът беше успешно обновен.";
$succesMsg["BG"][33] = "Свойството беше успешно обновено.";
$succesMsg["BG"][34] = "Информацията за доставка беше успешно обновена.";
$succesMsg["BG"][35] = "";
$succesMsg["BG"][36] = "Информацията за отстъпка беше успешно обновена.";
$succesMsg["BG"][37] = "Поръчката беше успешно обновена.";
$succesMsg["BG"][39] = "Езиковата информация беше успешно обновена.";
//DELETE DATA
$succesMsg["BG"][40] = "Потребителят беше успешно изтрит.";
$succesMsg["BG"][41] = "Категорията беше успешно изтрита.";
$succesMsg["BG"][42] = "Продуктът беше успешно изтрит.";
$succesMsg["BG"][43] = "Свойството беше успешно изтрито.";
$succesMsg["BG"][44] = "Информацията за доставка беше успешно изтрита.";
$succesMsg["BG"][45] = "Любимят предмет беше успешно изтрит.";
$succesMsg["BG"][46] = "Информацията за отстъпка беше успешно изтрита.";
// ------------BG----------- //
// ---------SUCCESS--------- //
?>