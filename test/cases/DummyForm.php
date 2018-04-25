<?php

/**
 * DummyForm class
 */

namespace ConiferTest;

use Conifer\Form\AbstractBase;

class DummyForm extends AbstractBase {
  public function process(array $_) {
    return true;
  }
}
