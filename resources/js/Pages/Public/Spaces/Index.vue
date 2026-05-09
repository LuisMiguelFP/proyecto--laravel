<script setup>
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
    spaces: Array,
    types: Array,
    activeType: String,
})

const logoutForm = useForm({})

function logout() {
    logoutForm.post('/logout')
}

function goToSpace(slug) {
    router.get('/spaces/' + slug)
}

function formatPrice(price) {
    if (!price) return 'Gratuito'
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price) + '/h'
}
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14] text-gray-300">
        <header class="bg-[#0F1219] border-b border-[#1E232D] shadow-sm">
            <div class="max-w-6xl mx-auto px-4 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Laboratorio de Electrónica</h1>
                    <p class="text-sm text-gray-400 mt-1">Selecciona tu nivel y reserva tu espacio</p>
                </div>
                <div class="flex items-center gap-4 text-sm font-medium">
                    <a href="/my-reservations" class="text-neon-green-500 hover:text-neon-green-400 font-semibold transition-colors">Mis reservas</a>
                    <button
                        @click="logout"
                        class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 rounded-lg transition-colors"
                    >
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-12">
            <div v-if="spaces.length" class="space-y-6">
                <div
                    v-for="space in spaces"
                    :key="space.id"
                    @click="goToSpace(space.slug)"
                    class="bg-[#0F1219] rounded-2xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D] hover:border-neon-green-500/30 hover:shadow-[0_4px_30px_rgba(34,197,94,0.1)] transition-all p-8 cursor-pointer"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ space.name }}</h2>
                            <p class="text-gray-400 mt-1">{{ space.description }}</p>
                        </div>
                        <span class="text-sm bg-neon-green-500/10 text-neon-green-400 px-3 py-1 rounded-full font-medium border border-neon-green-500/30">
                            {{ space.capacity }} personas
                        </span>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-[#1E232D]">
                        <span class="text-sm text-gray-400">Desde</span>
                        <span class="text-xl font-bold text-neon-green-500">{{ formatPrice(space.price_per_hour) }}</span>
                    </div>
                    <div class="mt-4 text-center">
                        <span class="inline-block w-full py-3 bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-semibold rounded-xl transition shadow-[0_0_15px_rgba(34,197,94,0.3)]">
                            Ver disponibilidad y reservar
                        </span>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20 text-gray-500">
                <p class="text-lg">No hay espacios disponibles.</p>
            </div>
        </main>
    </div>
</template>
