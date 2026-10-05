<script setup>
import LoginForm from './components/LoginForm.vue'
</script>

<template>
  <div>
    <h2>{{ $t('auth.login') }}</h2>
    <LoginForm />
    <p class="auth-switch">
      {{ $t('auth.noAccount') }}
      <router-link :to="{ name: 'register' }">{{ $t('auth.register') }}</router-link>
    </p>
  </div>
</template>
