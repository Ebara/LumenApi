<?php

namespace App\Http\Controllers;

class BalanceController extends Controller
{

    private $account = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (isset($_COOKIE['account'])) {
            $this->account = json_decode($_COOKIE['account'], true);
        }
    }

    public function index(Request $request) 
    {
        if (isset($_GET['account_id']) && $_GET['account_id'] > 0) {
            if (isset($this->account[$_GET['account_id']])) {
                return response()->json($this->account[$_GET['account_id']], 200);
            }
        }

        return response()->json(0, 404);
    }

    public function event() 
    {
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];

            switch ($type) {
                case 'deposit':

                    if (isset($_REQUEST['destination']) && isset($_REQUEST['amount'])) {
                        $destination = intval($_REQUEST['destination']);
                        $amount      = $_REQUEST['amount'];

                        if (isset($this->account[$destination])) {
                            $this->account[$destination] = $this->account[$destination] + $amount;
                        } else {
                            $this->account[$destination] = $amount;
                        }
    
                        $this->save();
                        return response()->json(['destination' => ['id' => strval($destination), 'balance' => $this->account[$destination]]], 201);
                    }

                    break;

                case 'withdraw':

                    if (isset($_REQUEST['origin']) && isset($_REQUEST['amount'])) {
                        $origin = intval($_REQUEST['origin']);
                        $amount = $_REQUEST['amount'];

                        if (isset($this->account[$origin]) && ($amount <= $this->account[$origin])) {
                            $this->account[$origin] = $this->account[$origin] - $amount;

                            $this->save();
                            return response()->json(['origin' => ['id' => strval($origin), 'balance' => $this->account[$origin]]], 201);
                        }
                    }

                    break;

                case 'transfer':

                    if (isset($_REQUEST['origin']) && isset($_REQUEST['amount']) && isset($_REQUEST['destination'])) {
                        $origin      = intval($_REQUEST['origin']);
                        $destination = intval($_REQUEST['destination']);
                        $amount      = $_REQUEST['amount'];

                        if (isset($this->account[$origin]) && ($amount <= $this->account[$origin])) {
                            if (isset($this->account[$destination])) {
                                $this->account[$origin]      = $this->account[$origin] - $amount;
                                $this->account[$destination] = $this->account[$destination] + $amount;
                            } else {
                                $this->account[$origin]      = $this->account[$origin] - $amount;
                                $this->account[$destination] = $amount;
                            }

                            $this->save();
                            return response()->json(['origin' => ['id' => strval($origin), 'balance' => $this->account[$origin]], 'destination' => ['id' => strval($destination), 'balance' => $this->account[$destination]]], 201);
                        }
                    }

                    break;
            }
        }

        return response()->json(0, 404);
    }

    public function save()
    {
        setcookie('account', json_encode($this->account), time()+3600);
    }

    //
}
