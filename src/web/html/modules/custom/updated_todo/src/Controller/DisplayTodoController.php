<?php

namespace Drupal\updated_todo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use \Drupal\Core\Cache\Cache;

/**
 * Returns a page
 */

class DisplayTodoController extends ControllerBase{
	/**
	* Returns a page containing all the tasks
	*
	* @return array
	* Returns a renderable array
	*/
     
	protected $currentUser;
	protected $connection;

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

public function getTasks(){
  
	// @TODO: Move query & results to a different function.
  $query = $this->connection->select('todo', 't');
  $query->fields('t', ['task_list']);
	$query->fields('t', ['task_id']);
  $query->condition('t.current_user', $this->currentUser->getDisplayName());
	

  $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
	$this->messenger()->addMessage(t("query is running."));

	$output = [
		'#theme' => 'displaytodo',
		'#results' => $results,
		'#cache' => [
			// @TODO: Use custom cache tags for query results.
			'tags' => ['todolist_tag'],
		],
	];

	// @TODO: Remove unnecessary tags.
	Cache::invalidateTags(['todo_tag']);

	//dump($results);die;
	return $output;
	
	}

}