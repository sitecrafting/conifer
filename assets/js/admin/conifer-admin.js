;(function($){
  var HOTKEY_LOCATIONS = {
    // g d -> Dashboard
    '100': '/wp-admin/index.php',

    // g p -> Posts
    '112': '/wp-admin/edit.php',
    // g P -> New Post
    '80': '/wp-admin/post-new.php',
    // g c -> Categories
    '99': '/wp-admin/edit-tags.php?taxonomy=category',
    // g t -> Tags
    '116': '/wp-admin/edit-tags.php',

    // g m -> Media
    '109': '/wp-admin/upload.php',

    // g a -> Pages
    '97': '/wp-admin/edit.php?post_type=page',
    // g A -> New Page
    '65': '/wp-admin/post-new.php?post_type=page',

    // g T -> Themes
    '84': '/wp-admin/themes.php',

    // g C -> Customizer
    '67': '/wp-admin/customize.php?return=' + location.pathname,

    // g l -> Plugins
    '108': '/wp-admin/plugins.php',

    // g L -> Install Plugin
    '108': '/wp-admin/plugin-install.php',

    // g u -> Users
    '117': '/wp-admin/users.php',

    // g g -> General Settings
    '103': '/wp-admin/options-general.php',

    // g w -> Writing
    '119': '/wp-admin/options-writing.php',

    // g r -> Reading
    '114': '/wp-admin/options-reading.php',

    // g i -> Discussion
    '105': '/wp-admin/options-discussion.php',

    // g k -> General Settings
    '107': '/wp-admin/options-permalink.php',

    // g h -> Home Page
    '104': '/',
  };

  $(window).keypress(function(e) {
    if (interpretAsCommandPrefix(e)) {
      // user pressed "g" while focused on the main window

      // register a short-lived keypress handler for each potential action
      $(window).keypress(handleGoto);

      setTimeout(function() {
        $(window).off({keypress: handleGoto});
      }, 1000);
    }
	});

  function interpretAsCommandPrefix(e) {
    return e.which === 103
      && (e.target.id === 'wpbody-content' || e.target.tagName === 'BODY');
  }

  function handleGoto(e) {
    $(window).off({keypress: handleGoto});

    var adminPage = HOTKEY_LOCATIONS[e.which];
    console.log(e.which);

    if (adminPage) {
      location = adminPage;
    }
  }
})(jQuery);
