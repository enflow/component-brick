<?php

namespace Enflow\Component\Brick\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BrickController extends BaseController
{
    public function index(Request $request)
    {
        return redirect($request->get('intended', config('brick.home_url', '/')));
    }

    public function receiver(Request $request)
    {
        $message = $request->input('message');
        $payload = $request->input('payload');

        if (empty($message)) {
            return ['error' => 'No message specified'];
        }

        $className = ucfirst($message['id']);
        $fqcn = "Enflow\\Component\\Brick\\Messages\\{$className}";

        if (!class_exists($fqcn)) {
            return ['error' => 'Unknown message ' . $message['id']];
        }

        unset($message['id']);

        ($fqcn::write($message))->response($payload);

        return [];
    }
}
