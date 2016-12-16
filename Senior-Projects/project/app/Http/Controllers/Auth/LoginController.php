<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login(Request $request)
    {

        $email = $request->email;
        $user = User::where("email", $email)->first();

        if($user->is_admin)
        {
            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }

        else
        {
            $this->validateLoginStudent($request);

            return $this->attemptLoginStudent($request);
        }

    }

    protected function authenticated($request, $user)
    {
        if($user->is_admin) {
            return redirect()->intended('/dashboard');
        }
        return redirect()->intended('/category');
    }

    protected function attemptLoginStudent (Request $request)
    {
        $user = User::where("email", $request->email)->first();
        $checkStudent_id= $user->student_id;

        if($request->student_id == $checkStudent_id)
        {
            Auth::loginUsingId($user->id);
            return $this->sendLoginResponse($request);
        }
        else{
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
    }


    protected function validateLoginStudent(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'student_id' => 'required',
        ]);
    }


    public function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/category';
    }


}
