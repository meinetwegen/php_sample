<script setup>
import RegisterForm from './components/RegisterForm.vue'
</script>

<template>
  <div>
    <h2>{{ $t('auth.register') }}</h2>
    <RegisterForm />
    <p class="auth-switch">
      {{ $t('auth.haveAccount') }}
      <router-link :to="{ name: 'login' }">{{ $t('auth.login') }}</router-link>
    </p>
  </div>
</template>
