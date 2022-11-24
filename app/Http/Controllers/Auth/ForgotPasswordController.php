<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['username' => 'required']);


        $response = $this->broker()->sendResetLink(
            $request->only('username')
        );

        if($response == Password::RESET_LINK_SENT){

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.forgot_password_text1'), 'success'));

            return $this->sendResetLinkResponse($request, $response);
        }else{
            return back()->withInput($request->only('username'))->withErrors(['forgotusername' => trans($response)]);
        }

    }
}
