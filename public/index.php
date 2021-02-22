<?php
require_once('../src/Repository/ParticipationRepository.php');
require_once('../vendor/autoload.php');

$partRepo = new ParticipationRepository();
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

$searchData = $_GET;
$res = $partRepo->getFilterData($searchData);
echo $twig->render('list.html.twig', array('data' => $res['data'], 'searchData' => $searchData));

