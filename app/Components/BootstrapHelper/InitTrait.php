<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/9
 * Time: 下午6:01
 */

namespace App\Components\BootstrapHelper;

trait InitTrait {
    protected function initTrait($options) {
        foreach($options as $key => $val) {
            if (!property_exists ($this, $key)) {
                throw new \RuntimeException('not found property exception ' . print_r ([
                        'class' => get_class ($this),
                        'key' => $key,
                        'val' => $val,
                        $val
                    ], 1));
            }
            $this->$key = $val;
        }
    }

    protected function mergeOpts($default, $new = []) {

        foreach($default as $key => $val) {
            if (!property_exists ($this, $key)) {
                continue;
            }
            if (!isset($new[$key])) {
                $new[$key] = $val;
            }
        }
        return $new;
    }
}
