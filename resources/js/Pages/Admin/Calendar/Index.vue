<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    spaces: Array,
    space: Object,
    calendar: Array,
    weekStart: String,
    prevWeek: String,
    nextWeek: String,
});

const statusColors = {
    pendiente: 'bg-yellow-500/10 text-yellow-500 border-yellow-500/30',
    confirmada: 'bg-neon-green-500/10 text-neon-green-500 border-neon-green-500/30',
    rechazada: 'bg-red-500/10 text-red-500 border-red-500/30',
    cancelada: 'bg-gray-500/10 text-gray-400 border-gray-500/30',
    finalizada: 'bg-blue-500/10 text-blue-500 border-blue-500/30',
};

function changeSpace(slug) {
    router.get('/admin/calendar', { space: slug, week: props.weekStart });
}

function changeWeek(week) {
    const params = { week };
    if (props.space) params.space = props.space.slug;
    router.get('/admin/calendar', params, { preserveScroll: true });
}
</script>

<template>
    <AppLayout title="Calendario Admin">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-white leading-tight">Calendario Semanal</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Space Selector -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-4 mb-6 flex items-center gap-4 border border-[#1E232D]">
                    <label class="text-sm font-medium text-gray-300">Espacio:</label>
                    <select
                        v-if="spaces.length"
                        @change="changeSpace($event.target.value)"
                        class="border-[#2A303C] bg-[#1E232D] text-white rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none"
                    >
                        <option v-for="s in spaces" :key="s.slug" :value="s.slug" :selected="space?.slug === s.slug">
                            {{ s.name }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-2">
                        <button @click="changeWeek(prevWeek)" class="px-3 py-1.5 border border-[#2A303C] text-gray-300 rounded-lg text-sm hover:bg-[#1E232D] transition-colors">
                            Anterior
                        </button>
                        <span class="text-sm font-bold text-neon-green-500">
                            {{ new Date(weekStart + 'T00:00:00').toLocaleDateString('es-CO', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                        </span>
                        <button @click="changeWeek(nextWeek)" class="px-3 py-1.5 border border-[#2A303C] text-gray-300 rounded-lg text-sm hover:bg-[#1E232D] transition-colors">
                            Siguiente
                        </button>
                    </div>
                </div>

                <!-- Calendar -->
                <div v-if="calendar.length" class="space-y-4">
                    <div v-for="day in calendar" :key="day.date" class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 border border-[#1E232D]">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase mb-3 capitalize">{{ day.label }}</h3>

                        <div v-if="day.reservations.length || day.blocks.length" class="space-y-2">
                            <!-- Reservations -->
                            <div
                                v-for="r in day.reservations"
                                :key="r.id"
                                :class="['rounded-lg border px-4 py-2 text-sm flex items-center justify-between', statusColors[r.status]]"
                            >
                                <div>
                                    <span class="font-bold">{{ r.start }} — {{ r.end }}</span>
                                    <span class="mx-2 text-gray-500">|</span>
                                    <span>{{ r.user_name }}</span>
                                </div>
                                <span class="text-xs font-medium">{{ r.status }}</span>
                            </div>

                            <!-- Blocked Slots -->
                            <div
                                v-for="b in day.blocks"
                                :key="b.id"
                                class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm text-red-500 flex items-center justify-between"
                            >
                                <span class="font-bold">{{ b.start }} — {{ b.end }}</span>
                                <span class="text-xs" v-if="b.reason">{{ b.reason }}</span>
                            </div>
                        </div>

                        <p v-else class="text-xs text-gray-600 italic">Sin reservas ni bloqueos</p>
                    </div>
                </div>

                <div v-else class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D] p-12 text-center text-gray-500">
                    Selecciona un espacio para ver el calendario.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
