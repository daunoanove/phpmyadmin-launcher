<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LauncherController extends Controller
{
    protected $launcher;

    public function __construct()
    {
        $this->launcher = app(\App\Launcher::class);
    }

    public function preparePhpMyAdminConfig()
    {
        try {
            $this->launcher->preparePhpMyAdminConfig();
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 406);
        }

        return response()->json(true);
    }

    public function instances()
    {
        $return = [];

        foreach ( $this->launcher->getInstances() as &$instance )
        {
            if ( array_key_exists('password', $instance) )
            {
                unset($instance['password']);
            }

            $return[] = $instance;
        }

        return response()->json($return);
    }

    public function checkInstanceConnection(Request $request)
    {
        try {
            $check = $this->launcher->checkInstanceConnection($request->input('k'));
        } catch(\App\Exceptions\Launcher\InstanceNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 406);
        }

        return response()->json($check);
    }

    public function launchInstance(Request $request)
    {
        try {
            $launch = $this->launcher->launch($request->input('k'));
        } catch(\App\Exceptions\Launcher\InstanceNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 406);
        }

        return response()->json($launch);
    }

    public function logout()
    {
        $this->launcher->logout();

        return redirect('/');
    }
}
