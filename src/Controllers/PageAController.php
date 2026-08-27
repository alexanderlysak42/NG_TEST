<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\GameResultRepository;
use App\Repositories\RegistrationRepository;
use App\Services\GameService;
use App\Support\Csrf;

class PageAController
{
    private RegistrationRepository $registrationRepository;
    private GameResultRepository $gameResultRepository;
    private GameService $gameService;

    public function __construct()
    {
        $this->registrationRepository = new RegistrationRepository();
        $this->gameResultRepository = new GameResultRepository();
        $this->gameService = new GameService();
    }

    public function show(string $token): void
    {
        $registration = $this->loadValidRegistration($token);
        if ($registration === null) {
            return;
        }

        $csrfToken = Csrf::token();
        $lastResult = null;
        $history = null;

        require __DIR__ . '/../View/page_a.php';
    }

    public function regenerate(string $token): void
    {
        $registration = $this->loadValidRegistration($token, checkCsrf: true);
        if ($registration === null) {
            return;
        }

        $newToken = $this->registrationRepository->regenerateToken((int) $registration['id']);
        header('Location: /p/' . $newToken);
    }

    public function deactivate(string $token): void
    {
        $registration = $this->loadValidRegistration($token, checkCsrf: true);
        if ($registration === null) {
            return;
        }

        $this->registrationRepository->deactivate((int) $registration['id']);

        require __DIR__ . '/../View/link_invalid.php';
    }

    public function play(string $token): void
    {
        $registration = $this->loadValidRegistration($token, checkCsrf: true);
        if ($registration === null) {
            return;
        }

        $result = $this->gameService->play();
        $this->gameResultRepository->save(
            (int) $registration['id'],
            $result['number'],
            $result['result'],
            $result['amount']
        );

        $csrfToken = Csrf::token();
        $lastResult = $result;
        $history = null;

        require __DIR__ . '/../View/page_a.php';
    }

    public function history(string $token): void
    {
        $registration = $this->loadValidRegistration($token);
        if ($registration === null) {
            return;
        }

        $csrfToken = Csrf::token();
        $lastResult = null;
        $history = $this->gameResultRepository->lastThree((int) $registration['id']);

        require __DIR__ . '/../View/page_a.php';
    }

    private function loadValidRegistration(string $token, bool $checkCsrf = false): ?array
    {
        $registration = $this->registrationRepository->findByToken($token);

        if ($registration === null || !$this->registrationRepository->isValid($registration)) {
            http_response_code(404);
            require __DIR__ . '/../View/link_invalid.php';
            return null;
        }

        if ($checkCsrf && !Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return null;
        }

        return $registration;
    }
}