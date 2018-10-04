/* globals cy, context */
describe('Conifer', () => {
  before(() => {
    cy.task('installTheme', 'search-test-theme')
  })

  context('The home page', () => {
    it('displays the front-page content', () => {
      cy.visit('/')

      cy.get('h1').should('contain', 'Home Page Title')
    });
  })

  context('Search', () => {
    it('searches post fields and meta-fields', () => {
      // search for "goo"
      cy.visit('/?s=goo+glue')

      cy.get('.search-results article').should('have.length', 8)
      cy.get('article:nth-of-type(1) h2').should('have.text', 'A post about goo glue')
      cy.get('article:nth-of-type(2) h2').should('have.text', 'A whole page about goo')
      cy.get('article:nth-of-type(3) h2').should('have.text', 'Search term double match in meta')
      cy.get('article:nth-of-type(4) h2').should('have.text', 'Search term in content')
      cy.get('article:nth-of-type(5) h2').should('have.text', 'Search term in meta')
      cy.get('article:nth-of-type(6) h2').should('have.text', 'Search term in meta: hello')
      cy.get('article:nth-of-type(7) h2').should('have.text', 'Another partial match in meta')
      cy.get('article:nth-of-type(8) h2').should('have.text', 'A Thing')
    })
  })

});
