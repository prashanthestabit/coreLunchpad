<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\HttpRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\AssignedTeacherRequest;
use App\Http\Requests\LoginRequest;

class MainController extends Controller
{
    protected $httpRepository;
    protected $http;

    const STUDENTREG = "/register/student";
    const LOGIN = '/auth/login';
    const TEACHERREG = "/register/teacher";
    const STUDENTAPPROVED = "/student/approved/";
    const TEACHERAPPROVED = "/teacher/approved/";
    const ASSIGNTEACHER = "/assigned/teacher";
    const ISADMIN = '/isAdmin';
    const NOTIFICATIONSTORE = '/notification/store';


    public function __construct(HttpRepository $httpRepository, array $options = [])
    {
        $this->http  = $httpRepository;
    }


    /**
     * Admin and student login API
     * @param email
     * @param password
     * @return $response
     */
    public function studentLogin(LoginRequest $request)
    {
        try{
            $url = config('app.student_url').self::LOGIN;
            $response = $this->http->post($url,$request->all());
            return $this->http->getResponse($response);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     * Teacher Login API
     * @param email
     * @param password
     * @return $response
     */
    public function teacherLogin(LoginRequest $request)
    {
        try{
            $url = config('app.teacher_url').self::LOGIN;

            $response = $this->http->post($url,$request->all());
            return $this->http->getResponse($response);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     * Student Approved By the Admin
     *
     */
    public function studentApproved(Request $request)
    {
        try{
            $header = $request->header('Authorization');
            $id = $request->id;
            if(empty($id) || !is_numeric($id))
            {
                return response()->json(['error' => __('messages.try_again')], 401);
            }

            $url = config('app.student_url').self::STUDENTAPPROVED.$id;
            $studentUrl = config('app.student_url').'/user/'.$id;

            if(empty($header))
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Check Student Approved already or not
            $student = $this->http->get($studentUrl,$header);
            if($student->status() === 200)
            {
                $studentData = $student->json('user');
                if(empty($studentData)){
                    return response()->json(['error' => 'Stunent Not Found'], 401);
                }

                $response = $this->http->get($url,$header);
                return $this->http->getResponse($response);

            }else{
                return $student->throw();
            }

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     * Teacher will be assigned to the student by the admin.
     */
    public function assignedTeacher(AssignedTeacherRequest $request)
    {
        try{
            $header = $request->header('Authorization');

            if(empty($header))
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $urlNew = config('app.student_url').self::ASSIGNTEACHER;

            $response = $this->http->post($urlNew,$request->all(),'',$header);

            if($response->status() === 200)
            {
                //notification for the teacher, when there is a new student assigned to him
                $this->sendNewStudentAssignedNotification($header,$request);
                return $response->json();
            }else{
                return $response->throw();
            }


            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     * Once the teacher completes his/her profile, the admin can able to approve this
     */
    public function teacherApproved(Request $request)
    {
        try{
            $header = $request->header('Authorization');

            if(empty($header))
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $id = $request->id;

            if(empty($id) || !is_numeric($id))
            {
                return response()->json(['error' => __('messages.try_again')], 401);
            }

            $url = config('app.student_url').self::ISADMIN;

            $response = $this->http->get($url,$header);

            if($response->status() === 200)
            {
                $urlForApprove = config('app.teacher_url').self::TEACHERAPPROVED.$id;

                $responseApproval = $this->http->get($urlForApprove);

                return $this->http->getResponse($responseApproval);
            }else{
                return $response->throw();
            }


            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }

    /**
     *
     */
    public function studentRegister(Request $request)
    {
        try{
            $url = config('app.student_url').self::STUDENTREG;
            $response = $this->http->post($url,$request->except('image'),$request->file('image'));

            return $this->http->getResponse($response);

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

            return $$this->http->getResponse($response);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => __('messages.error')], 500);
            }
    }


    protected function sendNewStudentAssignedNotification($header,$request)
    {
        try{

            $notificationUrl = config('app.notification_url').self::NOTIFICATIONSTORE;

            $notification = $this->http->post($notificationUrl,$request->all(),null,$header);

            if($notification->status() === 200)
            {
                Log::info('StudentAssignedNotification Send');
            }else{
                $notification->throw();
            }

            return true;
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => __('messages.error')], 500);
        }
    }
}
