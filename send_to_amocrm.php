<?php
// Функция для авторизации в AmoCRM
// Функция для создания сделки и контакта в AmoCRM
function createDealAndContact($name, $email, $phone, $price, $timeSpent) {
    $access_token = file_get_contents('access_token.txt');
    $your_amocrm_domain = 'danilgasilow';
    // URL для создания контакта
    $contactsUrl = "https://".$your_amocrm_domain.".amocrm.ru/api/v4/contacts";
    
    // Данные для создания контакта
    $contactData = [
        [
            'name' => $name,
            'custom_fields_values' => [
                [
                    'field_code' => 'EMAIL',
                    'values' => [
                        ['value' => $email]
                    ]
                ],
                [
                    'field_code' => 'PHONE',
                    'values' => [
                        ['value' => $phone]
                    ]
                ]
            ]
        ]
    ];
    
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token\r\nContent-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($contactData),
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($contactsUrl, false, $context);
    $contactResponse = json_decode($result, true);
    
    $contactId = $contactResponse['_embedded']['contacts'][0]['id'];
    
    // URL для создания сделки
    $leadsUrl = "https://".$your_amocrm_domain.".amocrm.ru/api/v4/leads";
    
    // Данные для создания сделки
    $leadData = [
        [
            'price' => $price,
            '_embedded' => [
                'contacts' => [
                    ['id' => $contactId]
                ]
            ],
            'custom_fields_values' => [
                [
                    'field_name' => 'Время на сайте',
                    'values' => [
                        ['value' => $timeSpent] // Передаем информацию о времени
                    ]
                ]
            ]
        ]
    ];
    
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token\r\nContent-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($leadData),
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($leadsUrl, false, $context);
    return json_decode($result, true);
}

// Получаем данные из запроса
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$price = $data['price'];
$timeSpent = $data['timeSpent'];

// Создаем сделку и контакт
$response = createDealAndContact($name, $email, $phone, $price, $timeSpent);

if (isset($response['_embedded']['leads'][0]['id'])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>
