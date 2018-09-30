<?php

use Timber\Timber;
use Conifer\Post\FrontPage;
use Conifer\Post\Post;
use Conifer\Util;

sleep(3);
$data = $site->get_context_with_post(FrontPage::get());
$data['posts'] = Timber::get_posts();
Util\debug(array_map(function($p){return "{$p->id} {$p->title}";}, $data['posts']));

Timber::render('index.twig', $data);
