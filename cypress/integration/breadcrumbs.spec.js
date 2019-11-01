/* globals cy, before */
describe('Breadcrumbs', () => {
  before(() => {
    cy.task('installTheme', 'breadcrumbs-test-theme')
    cy.task('installFixture', 'breadcrumb-pages')
  })

  it('searches post fields and meta-fields', () => {
    cy.visit('/')

    cy.get('nav')
  })
})

