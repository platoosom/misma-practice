<?php

namespace Appointment\Models;

class Appointment
{
    private $date;
    private $user;

    public function __construct($date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function sendRemender()
    {
        $email = [
            'subject' => 'Reminder!',
            'content' => 'Dear '. $this->user->getName().', You have an appointment on'. $this->date,
        ];

        mb_send_mail($this->user->getEmail(), $email['subject'], $email['content']);
    }
}