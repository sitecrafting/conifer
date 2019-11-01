<?php

use Timber\Timber;
use Conifer\Post\Page;
use Conifer\Navigation\NavBreadcrumbTrail;
use Conifer\Navigation\PostBreadcrumbTrail;
use Conifer\Navigation\CustomBreadcrumbTrail;

$data = $site->context([
  'post' => new Page(),
]);
$data['nav_breadcrumb_trail'] = new NavBreadcrumbTrail();

Timber::render('index.twig', $data);
