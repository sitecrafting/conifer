<?php

use Timber\Timber;
use Conifer\Post\FrontPage;
use Conifer\Post\Post;

$data = $site->get_context_with_post(FrontPage::get());

Timber::render('index.twig', $data);
