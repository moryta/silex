<?php
// web/index.php
use Silex\Application;

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(),array(
    'twig.path' => __DIR__.'/views',
));

//REGISTRA DoctrineServiceProvider
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
  'db.options' => array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'silex',
    'user' => 'root',
    'password' => '123456',
    'charset' => 'utf8mb4',
  ),
));

$carros = [
  "marcas" => ["Fiat","Chevrolet","hyundai","Nissan"],
  "modelos" => ["UNO","Agile","HB20","March"],
];

$app->get('/marcas',function() use ($carros,$app){
  return $app['twig']->render('marcas.twig',array(
    'marcas' => $carros['marcas']
  ));
});

//Exemplo de registro de log e utilizacao
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => __DIR__.'/silex.log',
));

//Executa a consulta e manda o resultado para o template posts.twig
$app->get('/posts/{id}', function ($id) use ($app){
  $sql = "SELECT * FROM posts WHERE id = ?";
  $post = $app['db']->fetchAssoc($sql,array((int) $id));

  $app['monolog']->addInfo(sprintf("O titulo do post e '%s'.", $post["title"]));//tipo informativo
  $app['monolog']->addWarning(sprintf("O titulo do post e '%s'.", $post["title"]));//tipo aviso
  $app['monolog']->AddError(sprintf("O titulo do post e '%s'.", $post["title"]));//tipo erro

  return $app['twig']->render('posts.twig',array(
    'posts' => $post,
  ));
});

$app->run();
