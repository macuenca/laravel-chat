<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    const USER_TYPE_REPRESENTATIVE = 'representative';
    const USER_TYPE_CUSTOMER = 'customer';

    /**
     * Display a list of chat representatives
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Make sure the user is logged-in
        $user = Auth::guard('api')->user();

        return User::where('type', self::USER_TYPE_REPRESENTATIVE)
            ->take(self::DEFAULT_PAGE_SIZE)
            ->get()
            ->toJson();
    }

    /**
     * Change a representative's name
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Make sure the logged-in user is a representative
        $user = Auth::guard('api')->user();
        if ($user->type != self::USER_TYPE_REPRESENTATIVE) {
            return Response::json([], 403);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->save();

        return $user->toJson();
    }
}
