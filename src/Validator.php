<?php


namespace App;


use App\Entity\Flight;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Validator
{
    public static function required(array $data, array $required)
    {
        foreach ($required as $value) {
            if(!array_key_exists($value, $data)) {
                throw new NotFoundHttpException('Param '.$value.' not exist');
            }
        }

        foreach ($data as $value) {
            if(empty($value)) {
                throw new NotFoundHttpException('Param '.$value.' is empty');
            }
        }
    }

    public static function paramExist($param, $params_available)
    {
        if (!in_array($param, $params_available)) {
            throw new NotFoundHttpException('Unknown payment type '.$param);
        }
    }

    public static function flightStatusToBuy(Flight $flight)
    {
        if($flight->getStatus() == 'flight_ticket_sales_completed') {
            throw new NotFoundHttpException('Flight tickets sales completed');
        }

        if($flight->getStatus() == 'flight_canceled') {
            throw new NotFoundHttpException('Flight canceled');
        }
    }
}