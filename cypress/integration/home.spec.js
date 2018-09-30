/* globals cy, context */
describe('The Home Page', () => {
  before(() => {
    cy.task('activateTheme', 'home-page')
    cy.task('installFixture', '$LANDO_MOUNT/test/themes/home-page/fixture.yaml')
  })

  it('displays the front-page content', () => {
    cy.visit('/')

    cy.get('h1').should('contain', 'Home Page Title')
  });
});
