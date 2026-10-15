// Logs in through the API and stores the token the same way the app does.
Cypress.Commands.add('login', (email, password) => {
  cy.request('POST', '/api/auth/login', { email, password }).then(({ body }) => {
    window.localStorage.setItem('magazine_shop_token', body.access_token)
  })
})
