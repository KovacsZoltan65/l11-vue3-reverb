<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\NewUserCreated;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

/**
 * Send email verification after user is created.
 *
 * This listener will send the user an email verification after the user is created.
 *
 * @package App\Listeners
 * @author Ezequiel H. B. <ezequielhectorb@gmail.com>
 */
class SendEmailVerification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * This method is responsible for sending an email verification
     * to the newly created user.
     *
     * @param NewUserCreated $event The event containing the user data.
     * @return void
     */
    public function handle(NewUserCreated $event): void
    {
        sleep(5);
        // Send an email to the user's email address using the SendMail mailable.
        Mail::to($event->user->email)
            ->send(new SendMail($event->user));
    }
}
