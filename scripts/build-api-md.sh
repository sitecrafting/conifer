#!/usr/bin/env bash

echo "Building API markdown..."

pwd
ls -la

./vendor/bin/phpdoc-md generate /app/lib/Conifer/Admin > docs/reference/admin.md
#/app/vendor/bin/phpdoc-md generate /app/lib/Conifer/AjaxHandler > docs/reference/ajaxhandler.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Authorization > docs/reference/authorization.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Form > docs/reference/form.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Integrations > docs/reference/integrations.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Navigation > docs/reference/navigation.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Notifier > docs/reference/notifier.md
#/app/vendor/bin/phpdoc-md generate /app/lib/Conifer/Post > docs/reference/post.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Shortcode > docs/reference/shortcode.md
./vendor/bin/phpdoc-md generate /app/lib/Conifer/Twig > docs/reference/twig.md

#/app/vendor/bin/phpdoc-md generate /app/lib/Conifer/AcfSearch.php > /app/docs/reference/acf-search.md
#/app/vendor/bin/phpdoc-md generate /app/lib/Conifer/Site.php > /app/docs/reference/site.md