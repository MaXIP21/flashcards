<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTeacherRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public User $newTeacher;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $newTeacher)
    {
        $this->newTeacher = $newTeacher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $activationUrl = route('admin.users.show', $this->newTeacher);

        return (new MailMessage)
                    ->subject('New Teacher Registration Requires Activation')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A new user has registered as a teacher and requires activation.')
                    ->line('**Teacher Name:** ' . $this->newTeacher->name)
                    ->line('**Email:** ' . $this->newTeacher->email)
                    ->action('Activate Teacher Account', $activationUrl)
                    ->line('Please review their registration and activate their account if appropriate.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'teacher_id' => $this->newTeacher->id,
            'teacher_name' => $this->newTeacher->name,
        ];
    }
}
