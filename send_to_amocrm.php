<?php
// Получаем данные из запроса
$data = json_decode(file_get_contents("php://input"), true);

// Извлекаем данные из запроса
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$price = $data['price'];
$timeSpent = $data['timeSpent'];

// Здесь происходит логика для создания контакта и сделки в AmoCRM
// Например, вызов функции createDealAndContact($name, $email, $phone, $price)

// Для теста просто возвращаем данные обратно в JSON
$response = [
    'status' => 'success',
    'message' => 'Заявка успешно обработана',
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'price' => $price,
    'timeSpentOnPage' => $timeSpent ? 'Пользователь провел на странице более 30 секунд' : 'Пользователь провел на странице менее 30 секунд'
];

// Возвращаем ответ клиенту
header('Content-Type: application/json');
echo json_encode($response);
?>
