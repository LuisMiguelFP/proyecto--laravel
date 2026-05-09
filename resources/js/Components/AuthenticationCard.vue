<script setup>
import { ref } from 'vue'
const props = defineProps({
    animated: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Animated background -->
        <div v-if="props.animated" class="animated-background" aria-hidden="true"></div>
        
        <!-- Contenedor del Logo -->
        <div>
            <slot name="logo" />
        </div>

        <!-- Contenedor del Formulario (El cuadro blanco/oscuro) -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-[#0F1219]/90 border border-[#1E232D] shadow-[0_20px_60px_rgba(0,0,0,0.6)] overflow-hidden sm:rounded-xl backdrop-blur-sm">
            <slot />
        </div>
    </div>
</template>