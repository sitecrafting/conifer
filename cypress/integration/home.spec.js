/* globals cy, before */
describe('The home page', () => {
  before(() => {
    cy.task('installTheme', 'home')
    cy.task('installFixture', 'home')
  })

  it('displays the front-page content', () => {
    cy.visit('/')

    cy.get('h1').should('contain', 'Home Page Title')
  });
})

