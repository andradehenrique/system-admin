<?php

namespace App\Helpers;

use Adianti\Widget\Base\TElement;

class HtmlHelper
{
    public static function createLabelByColor($value, $color): mixed
    {
        if (empty($color)) {
            return $value;
        }
        $div = new TElement('span');
        $div->class = "label rounded";
        $div->style = "background-color: {$color};";
        $div->add($value);
        return $div;
    }
}
