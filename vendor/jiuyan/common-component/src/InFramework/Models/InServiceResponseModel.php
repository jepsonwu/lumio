<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 16:42
 */

namespace Jiuyan\Common\Component\InFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class InServiceResponseModel extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['succ', 'msg', 'code', 'data', 'time'];

    public function isEmpty()
    {
        return !isset($this->succ) || !isset($this->data);
    }
}