describe('Catálogo', () => {
  it('lista produtos e adiciona ao carrinho', () => {
    cy.visit('/catalog')
    cy.get('[data-cy=product-card]', { timeout: 15000 }).should('have.length.at.least', 1)
    cy.contains(/R\$/).should('be.visible')
    cy.get('[data-cy=add-to-cart]').first().click()
    cy.url().should('include', '/cart')
    cy.get('[data-cy=cart-checkout]').should('be.visible')
  })

  it('busca pelo campo da navbar', () => {
    cy.visit('/')
    cy.get('[data-cy=nav-search]').type('Ryzen')
    cy.get('[data-cy=nav-search-submit]').click()
    cy.url().should('include', 'search=Ryzen')
    cy.get('[data-cy=product-card]', { timeout: 15000 }).should('have.length.at.least', 1)
    cy.contains(/Ryzen/i).should('be.visible')
  })
})
