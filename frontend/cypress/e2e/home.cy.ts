describe('Home', () => {
  it('mostra a marca e abre o catálogo', () => {
    cy.visit('/')
    cy.contains('SMARTY').should('be.visible')
    cy.contains('Hardware certo').should('be.visible')
    cy.get('[data-cy=cta-catalog]').click()
    cy.url().should('include', '/catalog')
    cy.contains('Peças e serviços').should('be.visible')
  })
})
