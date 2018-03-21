<?php

namespace App\Components\Helpers;
class SidebarHelper
{
    public static function getSidebars()
    {
        return config("admin.sidebars");
    }

}