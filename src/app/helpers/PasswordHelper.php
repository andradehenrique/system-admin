<?php

namespace App\Helpers;

use Adianti\Widget\Base\TElement;
use HPasswordValidator;

class PasswordHelper
{
    public static function hash($string, $algorithm = PASSWORD_ARGON2ID): string
    {
        return password_hash($string, $algorithm);
    }

    public static function verify($string, $hash): bool
    {
        return password_verify($string, $hash);
    }

    public static function helpTip(): string
    {
        $div = new TElement('div');
        $div->class = 'text-left';

        $list = new TElement('ul');
        $list->class = 'list-unstyled';

        $div->add(TElement::tag('p', _t('Your password must contain:')));

        $list->add(TElement::tag('li', _t('At least ^1 digits', HPasswordValidator::MIN_LENGTH)));
        $list->add(TElement::tag('li', _t('A maximum of ^1 digits', HPasswordValidator::MAX_LENGTH)));
        $list->add(TElement::tag('li', _t('At least 1 number')));
        $list->add(TElement::tag('li', _t('At least 1 capital letter')));
        $list->add(TElement::tag('li', _t('At least 1 lowercase letter')));
        $list->add(TElement::tag('li', _t('At least 1 special character')));

        $div->add($list);
        return $div->getContents();
    }
}
