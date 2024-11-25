<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $errors = [];

    // Проверка имени
    if (!preg_match("/^[А-Яа-яЁё\s]{3,50}$/u", $_POST['fullName'])) 
    {
        $errors[] = "Неверный формат имени";
    }

    // Проверка электронной почты
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
    {
        $errors[] = "Неверный формат электронной почты";
    }

    // Проверка возраста
    $age = intval($_POST['age']);
    if ($age < 18 || $age > 100)
    {
        $errors[] = "Возраст должен быть от 18 до 100";
    }

    // Проверка страны
    $validCountries = ['usa', 'canada', 'uk'];
    if (!in_array($_POST['country'], $validCountries)) 
    {
        $errors[] = "Неверный выбор страны";
    }

    // Проверка способа связи
    $validContactMethods = ['email', 'phone'];
    if (!in_array($_POST['contactMethod'], $validContactMethods)) 
    {
        $errors[] = "Неверный способ связи";
    }

    // Проверка согласия с условиями
    if (!isset($_POST['terms'])) 
    {
        $errors[] = "Необходимо согласие с условиями";
    }

    // Проверка пароля
    if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $_POST['password'])) 
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

    // Логирование данных
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $_POST
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