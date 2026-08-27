<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\RegistrationRepository;
use App\Support\Csrf;

class RegistrationController
{
    private RegistrationRepository $registrationRepository;

    public function __construct()
    {
        $this->registrationRepository = new RegistrationRepository();
    }

    public function showForm(): void
    {
        $csrfToken = Csrf::token();
        $errors = [];

        require __DIR__ . '/../Views/home.php';
    }

    public function register(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');

        $errors = [];
        if ($username === '' || mb_strlen($username) > 255) {
            $errors[] = 'Please provide a valid username';
        }
        if (!preg_match('/^\+?[0-9]{7,15}$/', $phoneNumber)) {
            $errors[] = 'Please provide a valid phone number';
        }

        if ($errors) {
            $csrfToken = Csrf::token();
            require __DIR__ . '/../Views/home.php';
            return;
        }

        $registration = $this->registrationRepository->create($username, $phoneNumber);

        header('Location: /p/' . $registration['token']);
    }
}