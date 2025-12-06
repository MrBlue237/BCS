<?php
session_start();
$view = new stdClass();
$view->pageTitle = 'Welcome to the BCS Placement Portal';
require_once('Views/index.phtml');
