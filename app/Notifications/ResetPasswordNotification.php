<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $expire = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Reset Password - Bali Kami TMS')
            ->greeting('Halo ' . ($notifiable->name ?: 'Pengguna') . ',')
            ->line('Kami menerima permintaan untuk mereset password akun Anda pada Bali Kami TMS.')
            ->action('Reset Password', $this->resetUrl($notifiable))
            ->line('Link ini akan kedaluwarsa dalam ' . $expire . ' menit.')
            ->line('Jika Anda tidak merasa meminta reset password, abaikan email ini. Password Anda tidak akan berubah.');
    }
}
