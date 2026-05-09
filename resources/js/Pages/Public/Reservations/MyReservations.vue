<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    reservations: Array,
})

const rescheduleForm = ref({
    slug: '',
    start_time: '',
    end_time: '',
})
const showReschedule = ref(false)
const rescheduling = ref(false)

const statusColors = {
    pendiente:  'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    confirmada: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    rechazada:  'bg-red-500/20 text-red-400 border border-red-500/30',
    cancelada:  'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    finalizada: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
}

const levelColors = {
    basica:   'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    estandar: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    gold:     'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
}

function formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price)
}

function openReschedule(r) {
    const parts = r.start_time.split(' ')
    const dateParts = parts[0].split('/')
    const startIso = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}T${parts[1]}`
    const endIso = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}T${r.end_time}`
    rescheduleForm.value = {
        slug: r.slug,
        start_time: startIso,
        end_time: endIso,
    }
    showReschedule.value = true
}

function submitReschedule() {
    Swal.fire({
        title: '¿Reagendar reserva?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, reagendar',
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db',
    }).then((result) => {
        if (!result.isConfirmed) return

        rescheduling.value = true
        router.put(`/my-reservations/${rescheduleForm.value.slug}/reschedule`, {
            start_time: rescheduleForm.value.start_time,
            end_time: rescheduleForm.value.end_time,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showReschedule.value = false
                rescheduling.value = false
                Swal.fire({
                    title: 'Reagendada',
                    text: 'Tu reserva ha sido reagendada exitosamente',
                    icon: 'success',
                    confirmButtonColor: '#22c55e',
                    background: '#0F1219',
                    color: '#d1d5db'
                })
            },
            onError: (errors) => {
                rescheduling.value = false
                if (errors.availability) {
                    Swal.fire({
                        title: 'Horario no disponible',
                        text: errors.availability,
                        icon: 'warning',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db'
                    })
                }
            }
        })
    })
}
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14]">
        <header class="bg-[#0F1219] shadow-sm border-b border-[#1E232D]">
            <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
                <h1 class="text-xl font-bold text-white">Mis reservas</h1>
                <a href="/" class="text-sm font-semibold text-neon-green-500 hover:text-neon-green-400 transition-colors">Volver al inicio</a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-8">
            <div v-if="reservations.length" class="space-y-4">
                <div
                    v-for="r in reservations"
                    :key="r.slug"
                    class="bg-[#0F1219] rounded-xl border border-[#1E232D] shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                >
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-white">{{ r.space_name }}</span>
                            <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', levelColors[r.level]]">
                                {{ r.level_label }}
                            </span>
                            <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusColors[r.status]]">
                                {{ r.status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400">{{ r.start_time }} — {{ r.end_time }}</p>
                        <p v-if="r.notes" class="text-xs text-gray-500">{{ r.notes }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-lg font-bold text-neon-green-500">{{ formatPrice(r.total_price) }}</p>
                            <p class="text-xs text-gray-500">total</p>
                        </div>
                        <button
                            v-if="r.status === 'pendiente'"
                            @click="openReschedule(r)"
                            class="px-3 py-1.5 text-xs font-semibold text-blue-400 border border-blue-500/30 rounded-lg hover:bg-blue-500/10 transition"
                        >
                            Reagendar
                        </button>
                    </div>
                </div>

                <!-- Modal Reagendar -->
                <div v-if="showReschedule" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                    <div class="bg-[#0F1219] rounded-xl border border-[#1E232D] shadow-[0_0_40px_rgba(0,0,0,0.6)] p-6 w-full max-w-md mx-4">
                        <h3 class="text-lg font-bold text-white mb-4">Reagendar reserva</h3>
                        <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nuevo inicio</label>
                            <input v-model="rescheduleForm.start_time" type="datetime-local"
                                class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-neon-green-500 [color-scheme:dark]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nuevo fin</label>
                            <input v-model="rescheduleForm.end_time" type="datetime-local"
                                class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-neon-green-500 [color-scheme:dark]" />
                        </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button @click="submitReschedule" :disabled="rescheduling || !rescheduleForm.start_time || !rescheduleForm.end_time"
                                class="flex-1 py-2 bg-neon-green-500 hover:bg-neon-green-400 disabled:opacity-50 text-[#0B0E14] font-bold rounded-lg transition">
                                {{ rescheduling ? 'Reagendando...' : 'Guardar cambios' }}
                            </button>
                            <button @click="showReschedule = false"
                                class="px-4 py-2 border border-[#2A303C] text-gray-300 rounded-lg text-sm hover:bg-[#1E232D] transition">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20 text-gray-500">
                <p class="text-lg font-medium">No tienes reservas aún</p>
                <a href="/" class="mt-3 inline-block text-sm font-semibold text-neon-green-500 hover:text-neon-green-400 transition-colors">Reservar ahora</a>
            </div>
        </main>
    </div>
</template>