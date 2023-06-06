<?php

namespace Drupal\updated_todo\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Cache\Cache;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class DeleteTaskForm extends ConfirmFormBase {

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
   * ID of the item to delete.
   *
   * @var int
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo: Do the deletion.
    
    
    $num_deleted = $this->connection->delete('todo')->condition('task_id', $this->id)->execute();
    Cache::invalidateTags(['todolist_tag']);
    $url = Url::fromRoute('updated_todo.display');
    $form_state->setRedirectUrl($url);
    $this->messenger()->addMessage(t("Successfully deleted a task %id", ['%id' => $this->id]));
}

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "delete_task_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('updated_todo.display');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete Task %id?', ['%id' => $this->id]);
  }

}
