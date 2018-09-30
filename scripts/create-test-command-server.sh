#!/usr/bin/env bash

cmd_server="$LANDO_MOUNT/wp/test-command.php"

  echo '<?php
  $cmd = $_POST["cmd"];
  $res = `{$cmd}; echo $?`;
  file_put_contents("testcmd.log", "$cmd\n$res");
  echo $res;
  ' > "$cmd_server"
