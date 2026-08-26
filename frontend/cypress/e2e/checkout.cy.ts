describe('Checkout', () => {
  it('completa um pedido com PIX e endereço via CEP', () => {
    cy.intercept('GET', '**/api/cep/**', {
      zip: '01001-000',
      street: 'Praça da Sé',
      number: null,
      complement: null,
      neighborhood: 'Sé',
      city: 'São Paulo',
      state: 'SP',
    }).as('cep')

    cy.login()
    cy.addFirstProductToCart()
    cy.get('[data-cy=cart-checkout]').click()
    cy.url().should('include', '/checkout')

    cy.get('[data-cy=checkout-name]').clear().type('Cliente Cypress')
    cy.get('[data-cy=address-zip]').clear().type('01001000')
    cy.wait('@cep')
    cy.get('[data-cy=address-street]').should('have.value', 'Praça da Sé')
    cy.get('[data-cy=address-city]').should('have.value', 'São Paulo')
    cy.get('[data-cy=address-number]').clear().type('100')
    cy.get('[data-cy=pay-PIX]').click()
    cy.get('[data-cy=checkout-submit]').scrollIntoView().click()

    cy.url({ timeout: 20000 }).should('include', '/order-success')
    cy.contains('Pedido confirmado').should('be.visible')
  })
})
