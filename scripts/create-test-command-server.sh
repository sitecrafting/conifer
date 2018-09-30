#!/usr/bin/env bash

#
# This script creates a PHP file in the webroot that executes any command
# that is POSTed to it. For reasons that should be obvious, this PHP file is
# never tracked in source control.
#

cmd_server="$LANDO_MOUNT/wp/test-command.php"


if ! [[ -f "$cmd_server" ]] ; then

cat <<EOF > "$cmd_server"
<?php

// bail it we're certain we're not in a dev/test environment
if (!getenv('LANDO_SERVICE_NAME')) {
  exit;
}

// get the raw post body
\$cmd = file_get_contents('php://input');

// execute the command that was sent over
\$res = \`{\$cmd} 2>&1\`;

// do some rudimentary logging
\$log = fopen('testcmd.log', 'a');
\$entry = sprintf("---\n%s\n%s\n%s\n", date('Y-m-d H:i:s'), \$cmd, \$res);
fwrite(\$log, \$entry);
fclose(\$log);

?>
EOF

fi
