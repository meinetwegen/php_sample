<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { BarElement, CategoryScale, Chart as ChartJS, LinearScale, Tooltip } from 'chart.js'

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip)

const props = defineProps({
  points: { type: Array, required: true },
  label: { type: String, default: '' },
})

const chartData = computed(() => ({
  labels: props.points.map((p) => p.date.slice(5)),
  datasets: [{ label: props.label, data: props.points.map((p) => p.total_cents / 100), backgroundColor: '#3b5bdb', borderRadius: 4 }],
}))

const options = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
</script>

<template>
  <div class="revenue-chart">
    <Bar :data="chartData" :options="options" />
  </div>
</template>
