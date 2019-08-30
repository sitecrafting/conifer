/* globals cy, before */
describe('Search', () => {
  before(() => {
    cy.task('installTheme', 'search-test-theme')
    cy.task('installFixture', 'meta-search')
  })

  it('searches post fields and meta-fields', () => {
    // search for "goo glue"
    cy.visit('/?s=goo+glue')

    // posts with search term in title should come first
    cy.get('article:nth-of-type(1) h2').should('have.text', 'A post about goo glue')
    cy.get('article:nth-of-type(2) h2').should('have.text', 'A whole page about goo')

    cy.get('.search-results').should('contain', 'Search term double match in meta')
    cy.get('.search-results').should('contain', 'Search term in content')
    cy.get('.search-results').should('contain', 'Search term in meta (*bye)')
    cy.get('.search-results').should('contain', 'Search term in meta (hello)')
    cy.get('.search-results').should('contain', 'Another partial match in meta')
    cy.get('.search-results').should('contain', 'A Thing')

    cy.get('.search-results').should('not.contain', 'This shouldn\'t show up at all')
    cy.get('.search-results').should('not.contain', 'This shouldn\'t either')
    cy.get('.search-results').should('not.contain', 'Another Thing')
    cy.get('.search-results').should('not.contain', 'Thing Draft')

    cy.get('.search-results article').should('have.length', 8)
  })
})

