<?php

namespace Modules\Account\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Account\Models\Account;

/**
 * Class AccountTransformer
 * @package namespace Modules\Account\Transformers;
 */
class AccountTransformer extends TransformerAbstract
{

    /**
     * Transform the \Account entity
     * @param \Account $model
     *
     * @return array
     */
    public function transform(Account $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
