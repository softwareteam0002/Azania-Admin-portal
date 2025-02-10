<?php

namespace App\Http\Controllers;

use App\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    public static function generatePassword(){
        $alphabets = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789.@%$";
        $password = array();
        $alphaLength = strlen($alphabets) - 1;
        for ($i=0; $i<8; $i++){
            $n = rand(0, $alphaLength);
            $password[] = $alphabets[$n];
        }
        return implode($password);
    }

    public static function generateCode(){
        $numbers = '123456789';
        $code = array();
        $codeLength = strlen($numbers) -1;
        for ($i=0; $i<6; $i++){
            $n = rand(0, $codeLength);
            $code[] = $numbers[$n];
        }

        if (!Operator::on('sqlsrv4')->where('operator_number', $code)->first()){
            return implode($code);
        }

        return '';
    }

}
