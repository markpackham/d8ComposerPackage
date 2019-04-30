<?php

namespace Drupal\drupalup_event_hook\EventSubscriber;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/*
Our event subscriber class
 */
class NodeArticleFormAlterEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            HookEventDispatcherInterface::FORM_ALTER => 'hookFormAlter',
        ];
    }

    /*
    Implements hook_form_alter()
     */
    public function hookFormAlter($event)
    {
        //kint($event->getFormId());die();
        if ($event->getFormId() == 'node_article_edit_form') {
            $form = $event->getForm();
            $form['special_title'] = [
                '#type' => 'markup',
                '#markup' => '<div class="info">Ex edited it</div>',
            ];
            $event->setForm($form);
        }
    }
}
