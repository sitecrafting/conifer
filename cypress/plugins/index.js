const request = require('request')
module.exports = (on, config) => {

  const TEST_COMMAND_URL = `${config.baseUrl}/test-command.php`

  on('task', {

    installTheme(slug) {
      const cmd = `wp theme activate ${slug}`
      request.post(TEST_COMMAND_URL, {
        body: cmd,
      })

      return null
    },

  })

}
