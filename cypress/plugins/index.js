const request = require('request')

module.exports = (on) => {

  on('task', {

    installTheme(slug) {
      const cmd = `wp theme activate ${slug}`
      request.post('https://appserver/test-command.php', {
        body: cmd,
      })

      return null
    },

  })

}
