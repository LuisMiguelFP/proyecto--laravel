<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    space: Object,
    prefill: Object,
    levels: Object,
    slotMinutes: Number,
    auth: Object,
})

const selectedLevel = ref(props.prefill?.level ?? 'basica')

const form = useForm({
    space_id:   props.space.id,
    level:      props.prefill?.level ?? 'basica',
    start_time: props.prefill?.start ? new Date(props.prefill.start).toISOString().slice(0, 16) : '',
    end_time:   props.prefill?.end   ? new Date(props.prefill.end).toISOString().slice(0, 16)   : '',
    user_name:  props.auth?.user?.name  ?? '',
    user_email: props.auth?.user?.email ?? '',
    notes:      '',
})

function selectLevel(key) {
    selectedLevel.value = key
    form.level = key
}

const levelColors = {
    basica:   'border-blue-500/50 bg-[#1E232D] shadow-[0_0_15px_rgba(59,130,246,0.1)]',
    estandar: 'border-neon-green-500/50 bg-[#1E232D] shadow-[0_0_15px_rgba(34,197,94,0.1)]',
    gold:     'border-yellow-500/50 bg-[#1E232D] shadow-[0_0_15px_rgba(234,179,8,0.1)]',
}

const levelBadge = {
    basica:   'bg-blue-500/20 text-blue-400',
    estandar: 'bg-neon-green-500/20 text-neon-green-400',
    gold:     'bg-yellow-500/20 text-yellow-400',
}

function formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price) + '/h'
}

function submit() {
    form.post('/reservations', {
        onSuccess: () => {
            Swal.fire({
                title: '¡Reserva creada!',
                text: 'Tu reserva ha sido registrada exitosamente',
                icon: 'success',
                confirmButtonColor: '#22c55e',
                background: '#0F1219',
                color: '#d1d5db'
            }).then(() => {
                router.get('/my-reservations');
            });
        },
        onError: () => {
            if (form.errors.availability) {
                Swal.fire({
                    title: 'Horario no disponible',
                    text: form.errors.availability,
                    icon: 'warning',
                    confirmButtonColor: '#22c55e',
                    background: '#0F1219',
                    color: '#d1d5db'
                });
            }
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14] py-10 px-4 text-gray-300">
        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <a href="/" class="text-sm text-neon-green-500 hover:text-neon-green-400 font-semibold transition-colors">Volver</a>
                <h1 class="text-2xl font-bold text-white mt-2">Reservar — {{ space.name }}</h1>
            </div>

            <!-- Error disponibilidad -->
            <div v-if="form.errors.availability"
                class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ form.errors.availability }}
            </div>

            <!-- Selección de nivel -->
            <div class="space-y-3">
                <h2 class="font-semibold text-white">Selecciona el nivel de equipamiento</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div
                        v-for="(info, key) in levels"
                        :key="key"
                        @click="selectLevel(key)"
                        :class="[
                            'rounded-xl border-2 p-4 cursor-pointer transition-all',
                            selectedLevel === key ? levelColors[key] : 'border-[#1E232D] bg-[#0F1219] hover:border-[#2A303C]'
                        ]"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span :class="['text-xs font-bold px-2 py-1 rounded-full', levelBadge[key]]">
                                {{ info.label }}
                            </span>
                            <span class="text-sm font-semibold text-white">
                                {{ formatPrice(info.price_per_hour) }}
                            </span>
                        </div>

                        <ul class="text-xs text-gray-400 space-y-1 mt-2">
                            <li v-for="tool in info.tools" :key="tool">- {{ tool }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-[#0F1219] rounded-xl border border-[#1E232D] p-6 space-y-5 shadow-[0_4px_25px_rgba(0,0,0,0.5)]">
                <h2 class="font-semibold text-white">Datos de la reserva</h2>

                <input type="hidden" v-model="form.space_id" />
                <input type="hidden" v-model="form.level" />

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Inicio</label>
                        <input v-model="form.start_time" type="datetime-local"
                            class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 text-white"
                            :class="{ 'border-red-400': form.errors.start_time }" />
                        <p v-if="form.errors.start_time" class="text-red-400 text-xs mt-1">{{ form.errors.start_time }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Fin</label>
                        <input v-model="form.end_time" type="datetime-local"
                            class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 text-white"
                            :class="{ 'border-red-400': form.errors.end_time }" />
                        <p v-if="form.errors.end_time" class="text-red-400 text-xs mt-1">{{ form.errors.end_time }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nombre completo</label>
                    <input v-model="form.user_name" type="text"
                        class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 text-white"
                        :class="{ 'border-red-400': form.errors.user_name }" />
                    <p v-if="form.errors.user_name" class="text-red-400 text-xs mt-1">{{ form.errors.user_name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Correo electrónico</label>
                    <input v-model="form.user_email" type="email"
                        class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 text-white"
                        :class="{ 'border-red-400': form.errors.user_email }" />
                    <p v-if="form.errors.user_email" class="text-red-400 text-xs mt-1">{{ form.errors.user_email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Notas (opcional)</label>
                    <textarea v-model="form.notes" rows="3"
                        class="w-full bg-[#1E232D] border border-[#2A303C] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 resize-none text-white" />
                </div>

                <button type="button" @click="submit" :disabled="form.processing"
                    class="w-full bg-neon-green-500 hover:bg-neon-green-400 disabled:opacity-50 text-[#0B0E14] font-bold py-3 rounded-xl transition shadow-[0_0_15px_rgba(34,197,94,0.3)]">
                    {{ form.processing ? 'Enviando...' : 'Confirmar reserva' }}
                </button>
            </div>
        </div>
    </div>
</template>
