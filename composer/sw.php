<?php

require_once 'vendor/autoload.php';

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="My API",
 *     version="1.0.0",
 *     description="An example API using Swagger and PHP",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 */
class MyApi {
    /**
     * @OA\Get(
     *     path="/hello",
     *     summary="Returns a hello message",
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function hello() {
        return "Hello, World!";
    }
}

// Swagger 문서 생성
$openapi = \OpenApi\scan(__DIR__);

// JSON으로 출력
header('Content-Type: application/json');
echo $openapi->toJson();
