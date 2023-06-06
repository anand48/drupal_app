<?php

/**
 * @file
 * A form to collect to do list
 */

namespace Drupal\updated_todo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;


class CreateTodoForm extends FormBase{
    
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
  	return 'updated_todo_form';
  }

  /**
  * {@inheritdoc}
  */

  public function buildForm(array $form, FormStateInterface $form_state){
    $form['todo_task'] = [
      '#type' => 'textfield',
      '#title' => t('Please Enter a task'),
      '#size' => 25,
      '#required' => TRUE,
    ];

    $form['submit'] = [
    '#type' => 'submit',
    '#value' => t('Add a task'),
    ];

    return $form;
    }

    /**
     * {@inheritdoc}
     */

  public function validateForm(array &$form, FormStateInterface $form_state){

  }

  public function submitForm(array &$form, FormStateInterface $form_state){
        
		$added_task = $form_state->getValues();
		$fields['task_list'] = $added_task['todo_task'];
		$fields['current_user'] = $this->currentUser->getDisplayName();

		$this->connection->insert('todo')->fields($fields)->execute();
		$this->messenger()->addMessage(t("Successfully added task"));
  }
}