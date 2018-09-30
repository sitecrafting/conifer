<?php

use Timber\Timber;
use Conifer\Post\FrontPage;

$page = FrontPage::get();
\Conifer\Util\sdebug('home page id: %d', $page->ID);
Timber::render('index.twig', $site->get_context_with_post($page));
