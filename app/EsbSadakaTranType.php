<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsbSadakaTranType extends Model
{
    //
    protected $table='tbl_sadaka_tran_types';

    public $timestamps=false;

    protected $connection='sqlsrv3';
	

}
