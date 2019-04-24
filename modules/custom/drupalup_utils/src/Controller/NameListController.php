<?php

namespace Drupal\drupalup_utils\Controller;

use Drupal\Core\Controller\ControllerBase;
use Nubs\RandomNameGenerator\Vgng;

/*
Our list with weird character names
 */
class NameListController extends ControllerBase
{

    public function content()
    {
        $generator = new Vgng();

        $list_of_weird_names = [];
        for ($i = 1; $i < 10; $i++) {
            $list_of_weird_names[] = $generator->getName();
        }

        return [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            //'#items' => ['Firsty', 'Secondo'],
            '#items' =>  $list_of_weird_names,
        ];
    }

}
