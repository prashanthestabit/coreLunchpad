<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\HttpRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\StudentRequest;

class MainController extends Controller
{
    protected $httpRepository;

    const STUDENTREG = "/register/student";
    const LOGIN = '/auth/login';
    const TEACHERREG = "/register/teacher";
    const STUDENTAPPROVED = "/student/approved/";
    const TEACHERAPPROVED = "/teacher/approved/";
    const ISADMIN = '/isAdmin';


    public function __construct(HttpRepository $httpRepository, array $options = [])
    {
        $this->http  = $httpRepository;
    }


    /**
     * Admin and student login API
     */
    public function studentLogin(Request $request)
    {
        try{
            $url = config('app.student_url').self::LOGIN;
            $response = $this->http->post($url,$request->all());
            return $response;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }


    public function teacherLogin(Request $request)
    {
        try{
            $url = config('app.teacher_url').self::LOGIN;

            $response = $this->http->post($url,$request->all());
            return $response;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    public function studentApproved(Request $request)
    {
        try{
            $header = $request->header('Authorization');
            $id = $request->id;

            $url = config('app.student_url').self::STUDENTAPPROVED.$id;

            $response = $this->http->get($url,$header);

            return $response;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    public function teacherApproved(Request $request)
    {
        try{
            $header = $request->header('Authorization');
            $id = $request->id;

            $url = config('app.student_url').self::ISADMIN;

            $response = $this->http->get($url,$header);

            if($response['status'])
            {
                return response()->json(['error' => __('messages.error')], 500);
            }
            $urlForApprove = config('app.teacher_url').self::TEACHERAPPROVED.$id;

             $responseApproval = $this->http->get($urlForApprove);

             return $responseApproval;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    public function studentRegister(Request $request)
    {
        try{
            $url = config('app.student_url').self::STUDENTREG;
            $response = $this->http->post($url,$request->except('image'),$request->file('image'));

            return $response;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     * API For Teacher Register
     */
    public function teacherRegister(Request $request)
    {
        try{
            $url = config('app.teacher_url').self::TEACHERREG;
            $response = $this->http->post($url,$request->except('image'),$request->file('image'));

            return $response;

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }
}
