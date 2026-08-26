declare namespace Cypress {
  interface Chainable {
    login(email?: string, password?: string): Chainable<void>
    addFirstProductToCart(): Chainable<void>
  }
}

Cypress.Commands.add('login', (email?: string, password?: string) => {
  const user = email ?? 'cliente@marketplace.test'
  const pass = password ?? 'password'

  cy.visit('/login')
  cy.get('[data-cy=email]').clear().type(user)
  cy.get('[data-cy=password]').clear().type(pass)
  cy.get('[data-cy=login-submit]').click()
  cy.get('[data-cy=nav-user]', { timeout: 15000 }).should('be.visible')
})

Cypress.Commands.add('addFirstProductToCart', () => {
  cy.visit('/catalog')
  cy.get('[data-cy=product-card]', { timeout: 15000 }).should('have.length.at.least', 1)
  cy.get('[data-cy=add-to-cart]').first().click()
  cy.contains('Carrinho').should('be.visible')
  cy.get('[data-cy=cart-checkout]').should('be.visible')
})
