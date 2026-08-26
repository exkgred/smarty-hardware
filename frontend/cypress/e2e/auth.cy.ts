describe('Autenticação', () => {
  it('recusa credenciais inválidas', () => {
    cy.visit('/login')
    cy.get('[data-cy=email]').type('nobody@marketplace.test')
    cy.get('[data-cy=password]').type('errada')
    cy.get('[data-cy=login-submit]').click()
    cy.get('[data-cy=toast]').should('contain', 'Credenciais inválidas')
    cy.get('[data-cy=nav-login]').should('be.visible')
  })

  it('entra como cliente', () => {
    cy.login()
    cy.get('[data-cy=nav-user]').should('be.visible')
    cy.get('[data-cy=nav-login]').should('not.exist')
  })

  it('entra como admin e abre o painel', () => {
    cy.login('admin@marketplace.test')
    cy.visit('/admin')
    cy.url().should('include', '/admin')
    cy.get('[data-cy=admin-header]').should('contain', 'Painel de Administração')
    cy.contains('Total de Pedidos').should('be.visible')
  })
})
