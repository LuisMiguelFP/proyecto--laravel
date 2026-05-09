<template>
  <div class="p-6 bg-[#0F1219] rounded-xl border border-[#1E232D] shadow-[0_4px_25px_rgba(0,0,0,0.5)]">
    <h3 class="text-lg font-bold text-white mb-4">Ejemplo de Composable: useCounter</h3>
    <p class="text-gray-300 mb-4">Contador: <span class="text-neon-green-500 font-bold text-2xl">{{ count }}</span></p>
    <div class="flex gap-3">
      <button 
        @click="increment" 
        class="px-4 py-2 bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-bold rounded-lg transition"
      >
        + Incrementar
      </button>
      <button 
        @click="decrement" 
        class="px-4 py-2 border border-red-500/30 text-red-400 hover:bg-red-500/10 rounded-lg transition"
      >
        - Decrementar
      </button>
    </div>
  </div>
</template>

<script setup>
import { useCounter } from '@/composables/useCounter.js'

// Usar el composable
const { count, increment, decrement } = useCounter()
// Cada componente tiene su propio contador
</script>
