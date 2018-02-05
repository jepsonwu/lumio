<?php

namespace Modules\Account\Presenters;

use Modules\Account\Transformers\AccountTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AccountPresenter
 *
 * @package namespace Modules\Account\Presenters;
 */
class AccountPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AccountTransformer();
    }
}
