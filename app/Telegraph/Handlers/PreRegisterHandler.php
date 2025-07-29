<?php

namespace App\Telegraph\Handlers;

use App\Models\BotChat;
use App\Models\Guest;
use App\Models\User;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;

class PreRegisterHandler extends WebhookHandler
{
    public function start(): void
    {
        $this->requestPhoneNumber();
    }

    public function handleMessage(): void
    {

        Log::debug('Message'.$this->message->contact());
        // $contact = $this->message->contact();

        // Log::debug('Message'.$this->message->contact());
        // if (! $contact) {
        //     $this->requestPhoneNumber('Не удалось получить номер. Попробуйте еще раз.');

        //     return;
        // }
        // $phone = $contact->phoneNumber();
        // $this->processPhoneNumber($phone, $contact);
    }

    protected function processPhoneNumber(string $phone, $contact): void
    {
        // Check in User table
        $user = User::where('phone', $phone)->first();
        if ($user) {
            $this->registerChat('staff', $user->id);
            app(StaffHandler::class)->handle($this->request, $this->bot);

            return;
        }

        // Check in Guest table
        $guest = Guest::where('phone', $phone)->first();
        if ($guest) {
            $this->registerChat('guest', null, $guest->id);
            app(GuestHandler::class)->handle($this->request, $this->bot);

            return;
        }

        // Create new guest
        $guest = Guest::create([
            'phone' => $phone,
            'name' => trim("{$contact->firstName()} {$contact->lastName()}"),
        ]);

        $this->registerChat('guest', null, $guest->id);
        app(GuestHandler::class)->handle($this->request, $this->bot);
    }

    protected function registerChat(string $type, ?int $userId = null, ?int $guestId = null): void
    {
        BotChat::create([
            'chat_id' => $this->chat->chat_id,
            'type' => $type,
            'user_id' => $userId,
        ]);
    }

    protected function requestPhoneNumber(string $message = 'Пожалуйста, отправьте свой номер телефона для регистрации.'): void
    {
        $this->chat->message($message)
            ->replyKeyboard([[
                ['text' => '📱 Отправить номер', 'request_contact' => true],
            ]])
            ->send();
    }
}
