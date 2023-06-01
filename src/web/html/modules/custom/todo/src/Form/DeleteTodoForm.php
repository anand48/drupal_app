<?php

namespace Drupal\todo\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Cache\Cache;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class DeleteTodoForm extends ConfirmFormBase {

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
    $form_state->set('para', $id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo: Do the deletion.
    $value = $form_state->get('para');
    $connection = \Drupal::database();
    $num_deleted = $connection->delete('tasks')->condition('tid', $value)->execute();
    Cache::invalidateTags(['todo_tag']);
    $url = Url::fromRoute('todo.display_tasks');
    $form_state->setRedirectUrl($url);
    $this->messenger()->addMessage(t("Successfully deleted a task %id", ['%id' => $this->id]));
}

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "delete_todo_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('todo.display_tasks');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete Task %id?', ['%id' => $this->id]);
  }

}
