<?php

$access_token = file_get_contents('access_token.txt');
$your_amocrm_domain = file_get_contents('domain.txt');
// Получаем или создаем пользовательское поле
try {
    $customFieldId = getOrCreateCustomField($access_token, $your_amocrm_domain);
    // echo "ID пользовательского поля 'Время на сайте': " . $customFieldId;
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}

// Функция для получения списка пользовательских полей для сделок
function getCustomFields($access_token, $your_amocrm_domain) {
    $customFieldsUrl = "https://".$your_amocrm_domain.".amocrm.ru/api/v4/leads/custom_fields";

    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token\r\nContent-Type: application/json\r\n",
            'method' => 'GET',
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($customFieldsUrl, false, $context);

    return json_decode($result, true);
}

// Функция для создания пользовательского поля "Время на сайте" в AmoCRM
function createCustomField($access_token, $your_amocrm_domain) {
    $customFieldsUrl = "https://".$your_amocrm_domain.".amocrm.ru/api/v4/leads/custom_fields";

    // Данные для создания поля "Время на сайте"
    $customFieldData = [
        [
            'name' => 'Время на сайте',
            'type' => 'checkbox', // Тип поля - checkbox для логического значения
        ]
    ];

    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token\r\nContent-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($customFieldData),
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($customFieldsUrl, false, $context);

    return json_decode($result, true);
}

// Получение ID пользовательского поля "Время на сайте" или создание его, если не существует
function getOrCreateCustomField($access_token, $your_amocrm_domain) {
    $customFields = getCustomFields($access_token, $your_amocrm_domain);

    foreach ($customFields['_embedded']['custom_fields'] as $field) {
        if ($field['name'] === 'Время на сайте') {
            // Поле уже существует, возвращаем его ID
            return $field['id'];
        }
    }
    
    // Поле не существует, создаем его
    $fieldResponse = createCustomField($access_token, $your_amocrm_domain);
    
    if (isset($fieldResponse['_embedded']['custom_fields'][0]['id'])) {
        return $fieldResponse['_embedded']['custom_fields'][0]['id'];
    } else {
        throw new Exception('Ошибка при создании пользовательского поля.');
    }
}

// Получаем токен доступа из файла

?>


<?php
// Функция для создания сделки и контакта в AmoCRM с использованием пользовательского поля "Время на сайте"
function createDealAndContact($name, $email, $phone, $price, $timeSpent, $customFieldId, $access_token, $your_amocrm_domain) {    
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

    // Данные для создания сделки с использованием пользовательского поля
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
                    'field_id' => $customFieldId, // ID пользовательского поля
                    'values' => [
                        ['value' => $timeSpent ? true : false] // Передаем логическое значение
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
$price = (int)$data['price'];
$timeSpent = $data['timeSpent'];

// Получаем ID пользовательского поля "Время на сайте"
try {
    $customFieldId = getOrCreateCustomField($access_token, $your_amocrm_domain);
    // Создаем сделку и контакт с переданным ID пользовательского поля
    $response = createDealAndContact($name, $email, $phone, $price, $timeSpent, $customFieldId, $access_token, $your_amocrm_domain);
    
    if (isset($response['_embedded']['leads'][0]['id'])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
