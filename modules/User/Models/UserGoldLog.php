<?php

namespace Modules\User\Models;

use Jiuyan\Tools\Business\EnvironmentTool;
use Prettus\Repository\Database\Eloquent\Model;

/**
 *
 * @property int id
 * @property int user_id
 * @property int in_gold
 * @property int out_gold
 * @property int operation
 * @property string extra
 * @property int created_at
 * @property string updated_at
 *
 * Class UserGoldLog
 * @package Modules\User\Models
 */
class UserGoldLog extends Model
{
    protected $primaryKey = "user_id";

    protected $connection = "user_ext";

    protected $table = "user_gold_log";

    public $timestamps = false;

    protected $fillable = [
        'id', 'user_id', 'in_gold', 'out_gold', 'operation', 'extra', 'created_at', 'updated_at'
    ];

    public function getTableCount()
    {
        return EnvironmentTool::isProduction() ? 1024 : (EnvironmentTool::isTest() ? 2 : 1);
    }

    public function getSliceField()
    {
        return "user_id";
    }

    public function toArray()
    {
//        if (($result = parent::toArray()) && isset($result['id'])) {
//            $result['id'] = EncryptTool::encryptId($result['id']);
//        }
//        return $result;
    }
}