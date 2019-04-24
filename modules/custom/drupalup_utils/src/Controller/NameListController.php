<?php

namespace Drupal\drupalup_utils\Controller;

use Drupal\Core\Controller\ControllerBase;

/*
Our list with weird character names
 */
class NameListController extends ControllerBase
{

    public function content()
    {
        return [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#items' => ['First', 'Second'],
        ];
    }

}
