<?php
session_start();

function validateFullName($fullName) 
{
    return preg_match("/^[А-Яа-яЁё\s]{3,50}$/u", $fullName);
}

function validateEmail($email) 
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateAge($age) 
{
    $age = intval($age);
    return $age >= 18 && $age <= 100;
}

function validateCountry($country)
{
    $validCountries = ['Россия', 'Казахстан', 'Беларусь'];
    return in_array($country, $validCountries);
}

function validateContactMethod($contactMethod) 
{
    $validContactMethods = ['email', 'phone'];
    return in_array($contactMethod, $validContactMethods);
}

function validateTerms($terms) 
{
    return isset($terms);
}

function validatePassword($password) 
{
    return preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $errors = [];

    // Валидация полей формы
    if (!validateFullName($_POST['fullName'])) 
    {
        $errors[] = "Неверный формат имени";
    }

    if (!validateEmail($_POST['email'])) 
    {
        $errors[] = "Неверный формат электронной почты";
    }

    if (!validateAge($_POST['age'])) 
    {
        $errors[] = "Возраст должен быть от 18 до 100";
    }

    if (!validateCountry($_POST['country'])) 
    {
        $errors[] = "Неверный выбор страны";
    }

    if (!validateContactMethod($_POST['contactMethod'])) 
    {
        $errors[] = "Неверный способ связи";
    }

    if (!validateTerms($_POST['terms'])) 
    {
        $errors[] = "Необходимо согласие с условиями";
    }

    if (!validatePassword($_POST['password'])) 
    {
        $errors[] = "Пароль не соответствует требованиям";
    }

    // Если есть ошибки, перенаправляем обратно на форму
    if (!empty($errors)) 
    {
        $_SESSION['form_errors'] = $errors;
        header('Location: index.html');
        exit();
    }

    // Хеширование пароля перед сохранением
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Логирование данных
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            'fullName' => $_POST['fullName'],
            'email' => $_POST['email'],
            'age' => $_POST['age'],
            'country' => $_POST['country'],
            'contactMethod' => $_POST['contactMethod'],
            'terms' => isset($_POST['terms']) ? 'yes' : 'no',
            'password' => $hashedPassword // Сохраняем только хеш пароля
        ]
    ];

    $logFile = 'form_submissions.json';
    $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
    $logs[] = $logData;
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Перенаправление на страницу успеха
    header('Location: success.html');
    exit();
}
?>
