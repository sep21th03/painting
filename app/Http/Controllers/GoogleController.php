<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;

use Exception;

use App\Models\User;

use Illuminate\Support\Facades\Auth;



class GoogleController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function redirectToGoogle()

    {

        return Socialite::driver('google')->redirect();
    }



    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function handleGoogleCallback()

    {

        try {



            $user = Socialite::driver('google')->user();



            $finduser = User::where('google_id', $user->id)->first();



            if ($finduser) {



                Auth::login($finduser);



                return redirect()->intended('admin'); // here
            } else {

                $newUser = User::create([

                    'name' => $user->name,

                    'email' => $user->email,

                    'google_id' => $user->id,

                    'avt_url' => $user->avatar,

                    'password' => encrypt('123456dummy')

                ]);



                Auth::login($newUser);



                return redirect()->intended('admin');  // here
            }
        } catch (Exception $e) {

            $e->getMessage();
        }
    }
}
