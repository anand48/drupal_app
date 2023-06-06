<?php

/**
 * @file
 * A form to collect to do lists
 */

namespace Drupal\todo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Cache\Cache;

class TodoForm extends FormBase{
    /**
     * {@inheritdoc}
     */

     public function getFormId(){
         return 'todo_form';
     }

     /**
      * {@inheritdoc}
      */

      public function buildForm(array $form, FormStateInterface $form_state){
         $form['todo_text'] = [
             '#type' => 'textfield',
             '#title' => t('Please enter a task'),
             '#size' => 25,
             '#required' => TRUE,
         ];
         $form['submit'] = [
             '#type' => 'submit',
             '#value' => t('Add'),
         ];

         return $form;
     }

     /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

        if (strlen($form_state->getValue('todo_text')) < 5) {
          $form_state->setErrorByName('todo_text', $this->t('Task is too short.'));
        }
  
      }

     /**
      * {@inheritdoc}
      */

     public function submitForm(array &$form, FormStateInterface $form_state){
        $submitted_task = $form_state->getValue('todo_text');
        //$this->messenger()->addMessage(t("Successfully added a task. Your task is @entry.",['@entry' => $submitted_task]));
         
         $field = $form_state->getValues();
         $fields['task_list'] = $field['todo_text'];

         $conn = \Drupal\Core\Database\Database::getConnection();
         $conn->insert('tasks')->fields($fields)->execute();
         $this->messenger()->addMessage(t("Successfully added a task. Your task is @entry.",['@entry' => $submitted_task]));
         Cache::invalidateTags(['todo_tag']);
         $url = Url::fromRoute('todo.display_tasks');
         $form_state->setRedirectUrl($url);
     }
}