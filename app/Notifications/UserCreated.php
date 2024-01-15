<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Plan;
use Illuminate\Support\HtmlString;

class UserCreated extends Notification
{
    use Queueable;

    protected $_email;
    protected $_password;
    protected $_plan_id;
    protected $_is_sub;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($email, $password, $plan_id, $is_sub = false)
    {
        //
        $this->_email = $email;
        $this->_password = $password;
        $this->_plan_id = $plan_id;
        $this->_is_sub = $is_sub;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $plan = Plan::find($this->_plan_id);
        if ($plan && $plan->email_content) {
            $html = $plan->email_content;
            $html = str_replace('%email', $this->_email, $html);
            $html = str_replace('%password', $this->_password, $html);
            if ($this->_is_sub) {
                $html = str_replace('We just want to thank you for your order and ensure that you got access.', '', $html);
            }
            return (new MailMessage)->subject('Welcome to VidGen')->line(new HtmlString($html));
        } else {
            return (new MailMessage)
                ->subject('Welcome to VidGen')
                ->line('You are now member of VidGen.')
                ->line('Your memberhip info:')
                ->action('Login here', url('https://vid-gen.com/login'))
                ->line('Email: ' . $this->_email)
                ->line('Password: ' . $this->_password)
                ->line('Thank you for using our application!')
                ->line('VidGen Team');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
