## Table of contents

- [\Conifer\Navigation\Menu](#class-conifernavigationmenu)
- [\Conifer\Navigation\MenuItem](#class-conifernavigationmenuitem)

<hr /><a id="class-conifernavigationmenu"></a>
### Class: \Conifer\Navigation\Menu

> Custom Menu class to add special nav behavior on top of TimberMenu instances.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>get_current_top_level_item()</strong> : <em>Conifer\MenuItem the current top-level MenuItem</em><br /><em>Get the top-level nav item that points, or whose ancestor points, to the current post</em> |

*This class extends \Timber\Menu*

<hr /><a id="class-conifernavigationmenuitem"></a>
### Class: \Conifer\Navigation\MenuItem

> Custom MenuItem class for adding special nav behavior to TimberMenuItem instances

| Visibility | Function |
|:-----------|:---------|
| public | <strong>display_children()</strong> : <em>boolean true if this Item has nav children AND represents the current page or an ancestor of the current page</em><br /><em>Whether to display this item's children. Typically for use in side navigation structures with lots of hierarchy.</em> |
| public | <strong>has_children()</strong> : <em>boolean true if this MenuItem has children.</em><br /><em>Whether this MenuItem has child MenuItems or not.</em> |
| public | <strong>points_to_current_post_or_ancestor()</strong> : <em>boolean</em><br /><em>Whether this item points to the current post, or an ancestor of the current post</em> |

*This class extends \Timber\MenuItem*

*This class implements \Timber\CoreInterface*

