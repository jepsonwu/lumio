<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 20:15
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Func;

use App\Support\Urls;

class ImgRenderFunc extends AbstractRenderFunc
{
    /**
     * @var
     */
    protected $link;

    /**
     * @var
     */
    protected $picField;

    /**
     * @var
     */
    protected $https;

    public function __construct($picField, $link = null, $https = false)
    {
        $this->picField = $picField;
        $this->link     = $link ?: $picField;
        $this->https    = $https;
    }

    public function invoke($model, $column)
    {
        return $this->renderWidget('table_img');
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        if (starts_with($this->link, 'http')) {
            return $this->link;
        }

        return $this->getFieldValue($this->link);
    }

    /**
     * @return mixed
     */
    public function getPicField()
    {
        $url = $this->getFieldValue($this->picField);

        return $this->https ? Urls::toHttps($url) : $url;
    }
}