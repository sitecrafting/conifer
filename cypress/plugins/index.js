const execSync = require('child_process').execSync

module.exports = (on) => {

  on('task', {

    installTheme(slug) {
      execSync(`wp theme activate --quiet ${slug}`)
      return null
    },

    installFixture(name) {
      execSync(`wp fixture install --yes test/fixtures/${name}.yaml`)
      return null
    },

  })

}
