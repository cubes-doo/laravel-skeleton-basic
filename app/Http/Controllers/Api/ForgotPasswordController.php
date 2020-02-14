<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Controller;

use App\Http\Resources\Json as JsonResource;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     *  Handles the request made when the User forgets his email address
     * 
     *  @return string
     *  @access public
     */
    public function forgotPassword() 
    {
        request()->validate([
            'email' => 'required|email'
        ]);
        
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->passwordResetCredentials()
        );

        return 
            $response == Password::RESET_LINK_SENT 
                ? $this->sendResetLinkResponse($response) 
                : $this->sendResetLinkFailedResponse($response);
    }

    /**
     *  Sends the response of a successful password reset link
     *
     *  @param  string  $response
     *  @return \Illuminate\Http\RedirectResponse
     *  @access protected
     */
    protected function sendResetLinkResponse($response)
    {
        return JsonResource::make()->withSuccess(__($response));
    }

    /**
     *  Sends the response of a failed password reset link
     *
     *  @param  \Illuminate\Http\Request
     *  @param  string  $response
     *  @return \Illuminate\Http\RedirectResponse
     *  @access protected
     */
    protected function sendResetLinkFailedResponse($response)
    {
        return JsonResource::make()->withError(__($response));
    }
    
    /**
     *  Gets the needed authorization credentials from the request for the 
     *  password reset
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return array
     *  @access protected
     */
    protected function passwordResetCredentials()
    {
        $credentials = request()->only('email');
        
        return $credentials;
    }
}
