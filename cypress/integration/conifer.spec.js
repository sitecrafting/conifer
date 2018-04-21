/* globals cy, context */
describe('Conifer', () => {

  context('The home page', () => {
    it('display the front-page content', () => {
      cy.visit('/')

      cy.get('h1').should('contain', 'Hello world!')
    });
  })

});
