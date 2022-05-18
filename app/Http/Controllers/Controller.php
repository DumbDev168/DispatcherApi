<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Lang;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $data
     * @param string|null $key
     * @param string $file
     * @return Response
     */
    public function successWithMsg($data = null,string $key = null, string $file = 'api_msg') : Response{
        $message = $key === null ? "" : Lang::get("{$file}.{$key}");
        return ResponseBuilder::asSuccess()
            ->withMessage($message)
            ->withData($data)
            ->build();
    }

    /**
     * @param $data
     * @param string $key
     * @param string $file
     * @param int $errorCode
     * @return Response
     */
    public function errorWithMsg($data = null, string $key, string $file = 'api_msg', int $errorCode = 402) : Response{
        $message = $key === null ? "" : Lang::get("{$file}.{$key}");
        return ResponseBuilder::asError($errorCode)
            ->withMessage($message)
            ->withData($data)
            ->build();
    }


    /**
     * @param string $message
     * @param int $errorCode
     * @return Response
     */
    public function errorMsg(string $message,int $errorCode = 402) : Response{
        return ResponseBuilder::asError($errorCode)
            ->withMessage($message)
            ->build();
    }

}
