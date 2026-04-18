<?php


namespace App\Domain\Users\Events;


use App\Domain\Users\Models\User;
use Illuminate\Queue\SerializesModels;

class NeedVerify
{
    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var User
     */
    public $user;

    public $code;

    /**
     * Create a new event instance.
     *
     * NeedVerify constructor.
     * @param $code
     */
    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }
}
