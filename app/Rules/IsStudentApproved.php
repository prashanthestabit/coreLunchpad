<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Repositories\HttpRepository;

class IsStudentApproved implements Rule
{
    public $token;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $http = new HttpRepository();
        if(!empty($this->token->header('Authorization')))
        {
            $studentUrl = config('app.student_url').'/user/'.$value;

            $response = $http->get($studentUrl,$this->token->header('Authorization'));
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
        return __('messages.student.must_approved');
    }
}
