<?php

/**
 * @file
 * A form to edit lists
 */

 namespace Drupal\updated_todo\Form;

 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;
 use Drupal\Core\Url;
 use Drupal\updated_todo\Form\CreateTodoForm;
 use \Drupal\Core\Cache\Cache;
 use Drupal\Core\Session\AccountProxy;
 use Symfony\Component\DependencyInjection\ContainerInterface;
 use Drupal\Core\Database\Connection;

 class EditTaskForm extends CreateTodoForm{

    private $currentUser;
	private $connection;

	/**
  * @param \Drupal\Core\Session\AccountInterface $currentUser
  */

    public function __construct(AccountProxy $currentUser, Connection $connection){
      $this->currentUser = $currentUser; 
	  $this->connection = $connection;
	}

	/**
   * {@inheritdoc}
   */
    public static function create(ContainerInterface $container) {
      // Instantiates this form class.
      return new static(
        // Load the service required to construct this class.
        $container->get('current_user'),
	    $container->get('database')
      );
    }

    
     /**
      * {@inheritdoc}
      */

      public function getFormId(){
        return 'edit_task_form';
    }

    /**
     * {@inheritdoc}
     */

     public function buildForm(array $form, FormStateInterface $form_state, $id = NULL){

        $db = $this->connection;
        $query = $db->query("SELECT task_list from todo WHERE task_id = $id");
        $result = $query->fetchAll();

        $form = parent::buildForm($form, $form_state);

        $form['todo_task'] = [
            '#type' => 'textfield',
            '#title' => t('Please update the task'),
            '#default_value' => $result[0]->task_list,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Update'),
        ];

        $form_state->set('para', $id);
        
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

    public function submitForm(array &$form, FormStateInterface $form_state, $id=NULL){
        $submitted_task = $form_state->getValue('todo_task');
         $value = $form_state->get('para');
         $field = $form_state->getValues();
         $fields['task_list'] = $submitted_task;
         
         $this->connection->update('todo')->fields(['task_list' => $submitted_task])->condition('task_id', $value)->execute();
         $this->messenger()->addMessage(t("Successfully updated task."));
         // @TODO: Clear cache per user.
         Cache::invalidateTags(['todolist_tag']);
         $url = Url::fromRoute('updated_todo.display');
         $form_state->setRedirectUrl($url);
    }

 }