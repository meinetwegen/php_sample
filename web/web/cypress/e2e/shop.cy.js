describe('Magazine shop', () => {
  it('shows the catalog to guests', () => {
    cy.visit('/')
    cy.get('.magazine-card').should('have.length.greaterThan', 0)
  })

  it('filters the catalog by search term', () => {
    cy.visit('/')
    cy.get('.the-filter input').first().type('quantum')
    cy.get('.magazine-card').should('have.length', 1)
  })

  it('redirects guests from the cart to the login page', () => {
    cy.visit('/cart')
    cy.url().should('include', '/login')
  })

  it('lets a customer register and add a magazine to the cart', () => {
    cy.visit('/register')
    cy.get('input[autocomplete="name"]').type('E2E Customer')
    cy.get('input[autocomplete="email"]').type(`e2e+${Date.now()}@example.com`)
    cy.get('input[autocomplete="new-password"]').type('secret-pass-123')
    cy.get('button[type="submit"]').click()
    cy.get('.magazine-card').first().find('button').click()
    cy.get('.topbar__cart .el-badge').should('contain', '1')
  })
})
