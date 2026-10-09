<script setup>
import { reactive, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import TheForm from '@/components/TheForm/TheForm.vue'
import TheInput from '@/components/TheInput/TheInput.vue'
import TheSelector from '@/components/TheSelector/TheSelector.vue'
import { ordersApi } from '@/plugins/api'
import { errorMessage, fieldErrors } from '@/utils/errors'

const store = useStore()
const router = useRouter()
const { t } = useI18n()

const form = reactive({
  shipping_name: store.state.auth.user?.name || '',
  shipping_address: '',
  shipping_city: '',
  shipping_zip: '',
  shipping_country: '',
  payment_method: 'card',
})
const paymentOptions = [
  { value: 'card', label: t('checkout.card') },
  { value: 'cash_on_delivery', label: t('checkout.cash_on_delivery') },
]
const errors = ref({})
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  errors.value = {}
  try {
    await ordersApi.checkout(form)
    await store.dispatch('cart/fetch')
    ElMessage.success(t('checkout.success'))
    router.push({ name: 'orders' })
  } catch (e) {
    errors.value = fieldErrors(e)
    error.value = Object.keys(errors.value).length ? '' : errorMessage(e)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <TheForm :submit-label="$t('checkout.placeOrder')" :loading="loading" :error="error" @submit="submit">
    <TheInput v-model="form.shipping_name" :label="$t('checkout.name')" :error="errors.shipping_name" />
    <TheInput v-model="form.shipping_address" :label="$t('checkout.address')" :error="errors.shipping_address" />
    <TheInput v-model="form.shipping_city" :label="$t('checkout.city')" :error="errors.shipping_city" />
    <TheInput v-model="form.shipping_zip" :label="$t('checkout.zip')" :error="errors.shipping_zip" />
    <TheInput v-model="form.shipping_country" :label="$t('checkout.country')" :error="errors.shipping_country" />
    <TheSelector v-model="form.payment_method" :options="paymentOptions" :label="$t('checkout.payment')" :error="errors.payment_method" />
  </TheForm>
</template>
