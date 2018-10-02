#!/usr/bin/env bash

echo "Building API markdown..."

pwd
ls -la

./vendor/bin/phpdoc-md generate lib/Conifer/Admin > docs/reference-admin.md -vvv
#./vendor/bin/phpdoc-md generate lib/Conifer/AjaxHandler > docs/reference-ajaxhandler.md
./vendor/bin/phpdoc-md generate lib/Conifer/Authorization > docs/reference-authorization.md -vv
./vendor/bin/phpdoc-md generate lib/Conifer/Form > docs/reference-form.md -v
./vendor/bin/phpdoc-md generate lib/Conifer/Integrations > docs/reference-integrations.md
./vendor/bin/phpdoc-md generate lib/Conifer/Navigation > docs/reference-navigation.md
./vendor/bin/phpdoc-md generate lib/Conifer/Notifier > docs/reference-notifier.md
#./vendor/bin/phpdoc-md generate lib/Conifer/Post > docs/reference-post.md
./vendor/bin/phpdoc-md generate lib/Conifer/Shortcode > docs/reference-shortcode.md
./vendor/bin/phpdoc-md generate lib/Conifer/Twig > docs/reference-twig.md

#./vendor/bin/phpdoc-md generate lib/Conifer/AcfSearch.php > /app/docs-reference/acf-search.md
#./vendor/bin/phpdoc-md generate lib/Conifer/Site.php > /app/docs-reference/site.md 

cd docs
ls -la

cat admin.md
