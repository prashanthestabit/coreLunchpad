<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Repositories\HttpRepository;

class IsTeacherApproved implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $url = config('app.teacher_url').'/user/'.$value;
        $http = new HttpRepository();
        $response = $http->get($url);
        if($response->status() === 200)
        {
            $response = $response->json('user');
            if($response && $response['status_id'] == 2){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.teacher.must_approved');
    }
}
