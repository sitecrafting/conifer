#!/usr/bin/env bash

echo "Building API markdown..."

./vendor/bin/phpdoc-md generate Conifer\\Admin\\Notice                  > docs/reference/admin-notice.md
./vendor/bin/phpdoc-md generate Conifer\\Admin\\Page                    > docs/reference/admin-page.md
./vendor/bin/phpdoc-md generate Conifer\\Admin\\SubPage                 > docs/reference/admin-subpage.md
./vendor/bin/phpdoc-md generate Conifer\\AjaxHandler\\AbstractBase      > docs/reference/ajaxhandler-abstractbase.md
./vendor/bin/phpdoc-md generate Conifer\\Authorization\\AbstractPolicy  > docs/reference/authorization-abstractpolicy.md
./vendor/bin/phpdoc-md generate Conifer\\Authorization\\PolicyInterface > docs/reference/authorization-policyinterface.md
./vendor/bin/phpdoc-md generate Conifer\\Authorization\\ShortcodePolicy > docs/reference/authorization-shortcodepolicy.md
./vendor/bin/phpdoc-md generate Conifer\\Authorization\\TemplatePolicy  > docs/reference/authorization-templatepolicy.md
./vendor/bin/phpdoc-md generate Conifer\\Authorization\\UserRoleShortcodePolicy > docs/reference/authorization-userroleshortcodepolicy.md
./vendor/bin/phpdoc-md generate Conifer\\Form\\AbstractBase             > docs/reference/form-abstractbase.md
./vendor/bin/phpdoc-md generate Conifer\\Integrations\\YoastIntegration > docs/reference/integrations-yoastintegration.md
./vendor/bin/phpdoc-md generate Conifer\\Navigation\\Menu               > docs/reference/navigation-menu.md
./vendor/bin/phpdoc-md generate Conifer\\Navigation\\MenuItem           > docs/reference/navigation-menuitem.md
./vendor/bin/phpdoc-md generate Conifer\\Notifier\\AdminNotifier        > docs/reference/notifier-adminnotifier.md
./vendor/bin/phpdoc-md generate Conifer\\Notifier\\EmailNotifier        > docs/reference/notifier-emailnotifier.md
#./vendor/bin/phpdoc-md generate Conifer\\Notifier\\SendsEmail           > docs/reference/notifier-sendsemail.md
./vendor/bin/phpdoc-md generate Conifer\\Notifier\\SimpleNotifier       > docs/reference/notifier-simplenotifier.md
./vendor/bin/phpdoc-md generate Conifer\\Post\\BlogPost                 > docs/reference/post-blogpost.md
./vendor/bin/phpdoc-md generate Conifer\\Post\\FrontPage                > docs/reference/post-frontpage.md
#./vendor/bin/phpdoc-md generate Conifer\\Post\\HasCustomAdminColumns    > docs/reference/post-hascustomadmincolumns.md
#./vendor/bin/phpdoc-md generate Conifer\\Post\\HasCustomAdminFilters    > docs/reference/post-hascustomadminfilters.md
#./vendor/bin/phpdoc-md generate Conifer\\Post\\HasTerms                 > docs/reference/post-hasterms.md
./vendor/bin/phpdoc-md generate Conifer\\Post\\Image                    > docs/reference/post-image.md
./vendor/bin/phpdoc-md generate Conifer\\Post\\Page                     > docs/reference/post-page.md
./vendor/bin/phpdoc-md generate Conifer\\Post\\Post                     > docs/reference/post-post.md
./vendor/bin/phpdoc-md generate Conifer\\Shortcode\\AbstractBase        > docs/reference/shortcode-abstractbase.md
./vendor/bin/phpdoc-md generate Conifer\\Shortcode\\Button              > docs/reference/shortcode-button.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\FormHelper               > docs/reference/twig-formhelper.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\HelperInterface          > docs/reference/twig-helperinterface.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\ImageHelper              > docs/reference/twig-imagehelper.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\NumberHelper             > docs/reference/twig-numberhelper.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\TermHelper               > docs/reference/twig-termhelper.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\TextHelper               > docs/reference/twig-texthelper.md
./vendor/bin/phpdoc-md generate Conifer\\Twig\\WordPressHelper          > docs/reference/twig-wordpresshelper.md
./vendor/bin/phpdoc-md generate Conifer\\Site                           > docs/reference/site.md
