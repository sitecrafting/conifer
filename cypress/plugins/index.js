const request = require('request')

module.exports = (on) => {

  on('task', {

    activateTheme(slug) {
      return runWpCommand(`wp theme activate ${slug}`)
    },

    installFixture(path) {
      return runWpCommand(`wp fixture install --yes ${path}`)
    },

  })

}


function runWpCommand(command) {
  return request.post('https://appserver/test-command.php', {
    body: command,
  })
}
