<?php

/**
 * @file
 * A form to edit lists
 */

 namespace Drupal\todo\Form;

 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;
 use Drupal\Core\Url;
 use Drupal\todo\Form\TodoForm;
 use Drupal\Core\Database\Database;
 use \Drupal\Core\Cache\Cache;

 class EditTodoForm extends TodoForm{


     /**
      * {@inheritdoc}
      */

      public function getFormId(){
        return 'edit_todo_form';
    }

    /**
     * {@inheritdoc}
     */

     public function buildForm(array $form, FormStateInterface $form_state, $slug = NULL){

        $db = \Drupal::database();
        $query = $db->query("SELECT task_list from tasks WHERE tid = $slug");
        $result = $query->fetchAll();

        //dump($result);
        $form = parent::buildForm($form, $form_state);

        $form['todo_text'] = [
            '#type' => 'textfield',
            '#title' => t('Please update the task'),
            '#size' => 25,
            '#default_value' => $result[0]->task_list,
            '#required' => TRUE,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Update'),
        ];

        $form_state->set('para', $slug);
        


        return $form;
    }

    /**
    * {@inheritdoc}
    */
   public function validateForm(array &$form, FormStateInterface $form_state) {
 
     }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state, $slug=NULL){
        $submitted_task = $form_state->getValue('todo_text');
        //$this->messenger()->addMessage(t("Successfully added a task. Your task is @entry.",['@entry' => $submitted_task]));
         
         $value = $form_state->get('para');
         $field = $form_state->getValues();
         $fields['task_list'] = $field['todo_text'];

         $conn = \Drupal\Core\Database\Database::getConnection();
         $conn->update('tasks')->fields(array('task_list' => $submitted_task))->condition('tid', $value)->execute();
         $this->messenger()->addMessage(t("Successfully updated a task.Your task is @entry.",['@entry' => $submitted_task]));
         Cache::invalidateTags(['todo_tag']);
         $url = Url::fromRoute('todo.display_tasks');
         $form_state->setRedirectUrl($url);
    }

 }