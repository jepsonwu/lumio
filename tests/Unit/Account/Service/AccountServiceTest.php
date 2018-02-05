<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/16
 * Time: 11:41
 */
namespace Tests\Unit\Account\Api\V100;

use Tests\ServiceTestCase;

class AccountServiceTest extends ServiceTestCase
{
    public function testArray()
    {
        $arr = [
            [
                'abc',
                'def'
            ],
            [
                1,
                2,
                3
            ],
            [
                [],
                [],
                []
            ],
            [
                1.23,
                2.34,
                3.45
            ]
        ];
        $response = [
            'data' => [
                [
                    'dt'
                ],
                [
                    'at'
                ]
            ],
            'code',
            'msg',
        ];
        $this->json('GET', 'http://test-in-lumio.in66.com/api/account/v1/index', []);
        $responseStr = $this->response->getContent();
        echo PHP_EOL . $responseStr . PHP_EOL;
        $this->seeJsonStructure($response);
        return;

        $res = json_encode($response);
        $this->seeJson($response);
        return;
        echo json_encode($arr) . PHP_EOL;
        array_splice($arr, 0, 1);
        echo json_encode($arr) . PHP_EOL;
        echo json_encode(array_values($arr)) . PHP_EOL;
    }

    public function test()
    {
        
    }
}
