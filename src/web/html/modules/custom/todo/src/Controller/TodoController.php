<?php
namespace Drupal\todo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use \Drupal\Core\Cache\Cache;


/**
 * Provides a response
 */

 class TodoController extends ControllerBase{
     /**
      * Returns a simple page
      *
      *@return array
      * A simple renderable array
      */

      public function myPage(){
          return [
              '#markup' => 'Form data has been submitted',
          ];
      }

      public function getDetails() {
          $db = \Drupal::database();
          $query = $db->select('tasks','n');
          $query->fields('n');
          //$query = $db->query("SELECT task_list from tasks");
          $response = $query->execute()->fetchAll();
          //return new JsonResponse($response);

          $rows = array();
          foreach ($response as $row => $content){
            $rows[] = array(
                'data' => array($content->tid,$content->task_list)
            );
          }

          $header = array('Task id','Task List');
          $output = array(
              //'#theme' => 'table',
              '#theme' => 'todolist',
              '#test_var' => 'test value',
              '#title' => 'Total task list',
              '#header' => $header,
              '#rows' => $rows,
              '#cache' => [
                'tags' => ['todo_tag'],
              ],
          );
          

          //dump($rows);

          return $output;
      }
 }