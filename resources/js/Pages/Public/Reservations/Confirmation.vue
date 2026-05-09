<script setup>
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const props = defineProps({
    reservation: Object,
});

Swal.fire({
    title: '¡Reserva creada!',
    text: 'Tu reserva ha sido registrada exitosamente',
    icon: 'success',
    confirmButtonColor: '#22c55e',
    background: '#0F1219',
    color: '#d1d5db',
    timer: 3000,
    showConfirmButton: false
});
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14] py-16 px-4">
        <div class="max-w-lg mx-auto">
            <div class="bg-[#0F1219] rounded-2xl shadow-[0_0_40px_rgba(34,197,94,0.1)] border border-[#1E232D] p-8 text-center">
                <div class="mx-auto size-16 bg-neon-green-500/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="size-8 text-neon-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">¡Reserva creada!</h1>
                <p class="text-gray-400 mb-8">Tu reserva ha sido registrada exitosamente.</p>

                <div class="bg-[#1E232D] rounded-xl p-6 text-left space-y-3 text-sm border border-[#2A303C]">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Espacio</span>
                        <span class="font-medium text-white">{{ reservation.space_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Nivel</span>
                        <span class="font-medium text-white">{{ reservation.level_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Fecha</span>
                        <span class="font-medium text-white">{{ reservation.start_time }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Hora fin</span>
                        <span class="font-medium text-white">{{ reservation.end_time }}</span>
                    </div>
                    <div class="border-t border-[#2A303C] pt-3 flex justify-between">
                        <span class="text-gray-400 font-medium">Total</span>
                        <span class="font-bold text-lg text-neon-green-500">{{ new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(reservation.total_price) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Estado</span>
                        <span class="text-sm px-2 py-0.5 rounded-full font-medium bg-yellow-500/20 text-yellow-500 border border-yellow-500/30">{{ reservation.status }}</span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mt-4">Se ha enviado un correo de confirmación a <strong>{{ reservation.user_email }}</strong></p>

                <div class="mt-8 flex gap-3">
                    <button
                        @click="router.get('/my-reservations')"
                        class="flex-1 py-2.5 bg-neon-green-500 hover:bg-neon-green-400 text-black font-bold rounded-xl transition shadow-[0_0_15px_rgba(34,197,94,0.3)]"
                    >
                        Ver mis reservas
                    </button>
                    <button
                        @click="router.get('/spaces')"
                        class="flex-1 py-2.5 border border-[#2A303C] hover:bg-[#1E232D] text-gray-300 font-bold rounded-xl transition"
                    >
                        Reservar otro
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
