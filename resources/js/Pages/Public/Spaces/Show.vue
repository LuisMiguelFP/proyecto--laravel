<script setup>
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
    space: Object,
    calendar: Array,
    upcomingSlots: Array,
    weekStart: String,
    prevWeek: String,
    nextWeek: String,
})

const logoutForm = useForm({})
function logout() { logoutForm.post('/logout') }

function selectSlot(slot, level = null) {
    router.get('/reservations/new', {
        space: props.space.slug,
        start: slot.start,
        level: level,
    })
}

function navigateWeek(week) {
    router.get('/spaces/' + props.space.slug, { week }, { preserveScroll: true })
}

const levels = {
    basica: {
        label: 'Básica',
        price: 15000,
        color: 'blue',
        tools: [
            'Cautín estándar x2',
            'Estaño para soldar',
            'Multímetro digital',
            'Pinzas y destornilladores',
            'Protoboard',
        ],
    },
    estandar: {
        label: 'Estándar',
        price: 25000,
        color: 'indigo',
        tools: [
            'Todo lo de Básica',
            'Osciloscopio digital',
            'Fuente de poder regulable',
            'Generador de señales',
            'Cautín de temperatura controlada',
            'Componentes SMD básicos',
        ],
    },
    gold: {
        label: 'Gold',
        price: 45000,
        color: 'yellow',
        tools: [
            'Todo lo de Estándar',
            'Máquina lavadora de circuitos',
            'Estación de soldadura SMD',
            'Analizador lógico',
            'Microscopio de inspección',
            'Impresora 3D',
            'Fresadora CNC básica',
        ],
    },
}

function formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price) + '/h'
}

const levelBg = {
    basica:   'bg-[#0F1219] border-blue-500/30 shadow-[0_0_15px_rgba(59,130,246,0.1)]',
    estandar: 'bg-[#0F1219] border-neon-green-500/30 shadow-[0_0_15px_rgba(34,197,94,0.1)]',
    gold:     'bg-[#0F1219] border-yellow-500/30 shadow-[0_0_15px_rgba(234,179,8,0.1)]',
}

const levelBadge = {
    basica:   'bg-blue-500/20 text-blue-400',
    estandar: 'bg-neon-green-500/20 text-neon-green-400',
    gold:     'bg-yellow-500/20 text-yellow-400',
}

const levelBtn = {
    basica:   'bg-blue-500 hover:bg-blue-400 text-white',
    estandar: 'bg-neon-green-500 hover:bg-neon-green-400 text-black',
    gold:     'bg-yellow-500 hover:bg-yellow-400 text-black',
}
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14] text-gray-300">
        <header class="bg-[#0F1219] border-b border-[#1E232D] shadow-sm">
            <div class="max-w-5xl mx-auto px-4 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="router.get('/spaces')" class="text-neon-green-500 hover:text-neon-green-400 transition-colors text-sm font-semibold">Volver</button>
                    <h1 class="text-xl font-bold text-white">{{ space.name }}</h1>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <a href="/my-reservations" class="text-neon-green-500 hover:text-neon-green-400 transition-colors font-semibold">Mis reservas</a>
                    <button @click="logout" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 rounded-lg text-sm transition-colors">
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 py-10 space-y-10">

            <!-- Info del espacio -->
            <div class="bg-[#0F1219] rounded-2xl border border-[#1E232D] p-6 shadow-[0_4px_25px_rgba(0,0,0,0.5)]">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Acerca del laboratorio</h2>
                        <p class="text-gray-400 mt-1 max-w-xl">{{ space.description }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Capacidad</p>
                        <p class="text-2xl font-bold text-white">{{ space.capacity }} personas</p>
                    </div>
                </div>
                <div v-if="space.rules" class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg text-sm text-yellow-400">
                    <span class="font-semibold">Reglas: </span>{{ space.rules }}
                </div>
            </div>

            <!-- Planes -->
            <div>
                <h2 class="text-xl font-bold text-white mb-5">Elige tu plan de equipamiento</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="(info, key) in levels"
                        :key="key"
                        :class="['rounded-2xl border-2 p-6 flex flex-col gap-4', levelBg[key]]"
                    >
                        <div class="flex items-center justify-between">
                            <span :class="['text-sm font-bold px-3 py-1 rounded-full', levelBadge[key]]">
                                {{ info.label }}
                            </span>
                            <span class="text-lg font-bold text-white">{{ formatPrice(info.price) }}</span>
                        </div>

                        <ul class="space-y-2 flex-1">
                            <li
                                v-for="tool in info.tools"
                                :key="tool"
                                class="flex items-start gap-2 text-sm text-gray-300"
                            >
                                <span class="mt-0.5 text-neon-green-500 font-bold">+</span>
                                {{ tool }}
                            </li>
                        </ul>

                        <!-- Slots disponibles por plan -->
                        <div class="pt-3 border-t border-[#1E232D]">
                            <p class="text-xs font-semibold text-gray-500 mb-2 uppercase">Próximos horarios</p>
                            <div v-if="upcomingSlots.length" class="space-y-1">
                                <button
                                    v-for="slot in upcomingSlots.slice(0, 3)"
                                    :key="slot.start"
                                    @click="selectSlot(slot, key)"
                                    :class="['w-full text-left text-xs px-3 py-2 rounded-lg border hover:opacity-80 transition font-medium', levelBadge[key].replace('text-', 'border-').replace('/20', '/30')]"
                                >
                                    {{ new Date(slot.start).toLocaleDateString('es-CO', { weekday: 'short', day: 'numeric', month: 'short' }) }}
                                    — {{ slot.start_label }} a {{ slot.end_label }}
                                </button>
                            </div>
                            <p v-else class="text-xs text-gray-500">Sin horarios disponibles</p>
                        </div>

                        <button
                            @click="selectSlot(upcomingSlots[0] ?? { start: '' }, key)"
                            :class="['w-full py-2.5 text-white font-semibold rounded-xl text-sm transition', levelBtn[key]]"
                        >
                            Reservar plan {{ info.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendario semanal -->
            <div class="bg-[#0F1219] rounded-2xl border border-[#1E232D] p-6 shadow-[0_4px_25px_rgba(0,0,0,0.5)]">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-white">Disponibilidad semanal</h2>
                    <div class="flex items-center gap-3">
                        <button @click="navigateWeek(prevWeek)" class="px-3 py-1 border border-[#2A303C] rounded-lg text-sm hover:bg-[#1E232D] text-gray-300 transition-colors">
                            Anterior
                        </button>
                        <span class="text-sm font-bold text-neon-green-500">
                            {{ new Date(weekStart + 'T00:00:00').toLocaleDateString('es-CO', { day: 'numeric', month: 'long' }) }}
                        </span>
                        <button @click="navigateWeek(nextWeek)" class="px-3 py-1 border border-[#2A303C] rounded-lg text-sm hover:bg-[#1E232D] text-gray-300 transition-colors">
                            Siguiente
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-for="day in calendar" :key="day.date">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1 capitalize">{{ day.label }}</p>
                        <div v-if="day.slots.length" class="flex flex-wrap gap-2">
                            <button
                                v-for="slot in day.slots"
                                :key="slot.start"
                                @click="selectSlot(slot)"
                                class="px-3 py-1.5 text-sm rounded-lg bg-neon-green-500/10 hover:bg-neon-green-500/20 border border-neon-green-500/30 text-neon-green-400 font-medium transition-colors"
                            >
                                {{ slot.start_label }} - {{ slot.end_label }}
                            </button>
                        </div>
                        <p v-else class="text-xs text-gray-600 italic">Sin disponibilidad</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>
