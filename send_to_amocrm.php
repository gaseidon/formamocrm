<?php
// Функция для авторизации в AmoCRM
function getAccessToken() {
    // Настройки приложения AmoCRM
    $client_id = 'your_client_id';
    $client_secret = 'your_client_secret';
    $redirect_uri = 'your_redirect_uri';
    $authorization_code = 'your_authorization_code';
    
    $url = "https://your_amocrm_domain.amocrm.ru/oauth2/access_token";
    
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $authorization_code,
        'redirect_uri' => $redirect_uri,
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
    
    return $response['access_token'];
}

// Функция для создания сделки и контакта в AmoCRM
function createDealAndContact($name, $email, $phone, $price, $timeSpent) {
    $access_token = getAccessToken();
    
    // URL для создания контакта
    $contactsUrl = "https://your_amocrm_domain.amocrm.ru/api/v4/contacts";
    
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
    $leadsUrl = "https://your_amocrm_domain.amocrm.ru/api/v4/leads";
    
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
