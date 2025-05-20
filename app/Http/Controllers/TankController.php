<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TankController extends Controller
{
    protected $apiHelper;

    public function __construct()
    {
        $this->apiHelper = new ListFunctionController();
    }

    public function index()
    {
        $token = session('access_token');

        $url = env('API_APP') . 'integration/tanks';
        $tanks = $this->apiHelper->getAPIAuth($url, [], $token);

        if (!is_array($tanks)) {
            $tanks = [];
        }

        return view('tanks.index', compact('tanks'));
    }


    public function show($id)
    {
        $token = session('access_token');
        $url   = env('API_APP') . "integration/tanks/{$id}";
        $tank  = $this->apiHelper->getAPIAuth($url, [], $token);

        if (!is_object($tank)) {
            abort(404, 'Không tìm thấy bể chứa');
        }

        return view('tanks.show', compact('tank'));
    }
}
