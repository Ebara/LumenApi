<?php

namespace App\Http\Controllers;

class ResetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * reset
     */
    public function reset() 
    {
        $this->cleanCookie();
        return response()->json('Ok', 200);
    }

    /**
     * clear cookie
     * unset variable
     */
    private function cleanCookie() 
    {
        if (isset($_COOKIE['account'])) {
            unset($_COOKIE['account']); 
            setcookie('account', null, -1, '/'); 
        }
    }

}
