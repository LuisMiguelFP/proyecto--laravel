<script setup>
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reservations: Object,
    spaces: Array,
    filters: Object,
    statuses: Array,
});

const statusColors = {
    pendiente: 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/30',
    confirmada: 'bg-neon-green-500/10 text-neon-green-500 border border-neon-green-500/30',
    rechazada: 'bg-red-500/10 text-red-500 border border-red-500/30',
    cancelada: 'bg-gray-500/10 text-gray-400 border border-gray-500/30',
    finalizada: 'bg-blue-500/10 text-blue-500 border border-blue-500/30',
};

function filter(field, value) {
    router.get('/admin/reservations', { ...props.filters, [field]: value || undefined }, { preserveScroll: true });
}

function accept(slug) {
    Swal.fire({
        title: '¿Aceptar reserva?',
        text: "La reserva será confirmada",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, aceptar',
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/admin/reservations/${slug}/accept`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Aceptada',
                        text: 'La reserva ha sido confirmada',
                        icon: 'success',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db'
                    });
                }
            });
        }
    });
}

function reject(slug) {
    Swal.fire({
        title: '¿Rechazar reserva?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/admin/reservations/${slug}/reject`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Rechazada',
                        text: 'La reserva ha sido rechazada',
                        icon: 'error',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db'
                    });
                }
            });
        }
    });
}

function cancel(slug) {
    Swal.fire({
        title: '¿Cancelar reserva?',
        text: "La reserva será cancelada",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'Volver',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/admin/reservations/${slug}/cancel`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Cancelada',
                        text: 'La reserva ha sido cancelada',
                        icon: 'info',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db'
                    });
                }
            });
        }
    });
}

function remove(slug) {
    Swal.fire({
        title: '¿Eliminar permanentemente?',
        text: "Esta acción no se puede deshacer",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/reservations/${slug}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Eliminada',
                        text: 'La reserva ha sido eliminada permanentemente',
                        icon: 'success',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db'
                    });
                }
            });
        }
    });
}
</script>

<template>
    <AppLayout title="Reservas — Admin">
        <template #header>
            <h2 class="font-bold text-xl text-white leading-tight">Gestión de Reservas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-4 mb-6 flex flex-wrap items-center gap-4 border border-[#1E232D]">
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Estado</label>
                        <select
                            @change="filter('status', $event.target.value)"
                            class="border-[#2A303C] bg-[#1E232D] text-white rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none"
                        >
                            <option value="">Todos</option>
                            <option v-for="s in statuses" :key="s" :value="s" :selected="filters.status === s">{{ s }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Espacio</label>
                        <select
                            @change="filter('space', $event.target.value)"
                            class="border-[#2A303C] bg-[#1E232D] text-white rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none"
                        >
                            <option value="">Todos</option>
                            <option v-for="s in spaces" :key="s.slug" :value="s.slug" :selected="filters.space === s.slug">{{ s.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Fecha</label>
                        <input
                            type="date"
                            :value="filters.date"
                            @change="filter('date', $event.target.value)"
                            class="border-[#2A303C] bg-[#1E232D] text-white rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none"
                        />
                    </div>
                    <button
                        v-if="filters.status || filters.space || filters.date"
                        @click="router.get('/admin/reservations')"
                        class="mt-5 px-3 py-1.5 text-sm text-neon-green-500 border border-neon-green-500/30 rounded-lg hover:bg-neon-green-500/10 transition-colors"
                    >
                        Limpiar filtros
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D] overflow-hidden">
                    <div v-if="reservations.data.length" class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-300">
                            <thead class="bg-[#1E232D] text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Espacio</th>
                                    <th class="px-4 py-3 text-left">Usuario</th>
                                    <th class="px-4 py-3 text-left">Fecha / Hora</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="r in reservations.data" :key="r.id" class="border-b border-[#1E232D] hover:bg-[#1E232D] transition-colors">
                                    <td class="px-4 py-3 font-bold text-white">{{ r.space_name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-white">{{ r.user_name }}</span>
                                        <br><span class="text-xs text-gray-500">{{ r.user_email }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ r.start_time }}<br><span class="text-xs text-gray-500">{{ r.end_time }}</span></td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="['text-xs px-2 py-1 rounded-full font-medium', statusColors[r.status]]">{{ r.status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <template v-if="r.status === 'pendiente'">
                                            <button @click="accept(r.slug)" class="px-2 py-1 text-xs font-bold text-black bg-neon-green-500 hover:bg-neon-green-400 rounded mr-1 transition shadow-[0_0_10px_rgba(34,197,94,0.2)]">Aceptar</button>
                                            <button @click="reject(r.slug)" class="px-2 py-1 text-xs font-bold text-white bg-red-500 hover:bg-red-400 rounded mr-1 transition shadow-[0_0_10px_rgba(239,68,68,0.2)]">Rechazar</button>
                                        </template>
                                        <template v-if="r.status === 'confirmada'">
                                            <button @click="cancel(r.slug)" class="px-2 py-1 text-xs font-bold text-white bg-gray-600 hover:bg-gray-500 rounded mr-1 transition border border-gray-500/50">Cancelar</button>
                                        </template>
                                        <button @click="remove(r.slug)" class="px-2 py-1 text-xs font-bold text-red-400 border border-red-500/30 hover:bg-red-500/10 rounded transition-colors">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500">
                        No se encontraron reservas.
                    </div>

                    <!-- Pagination -->
                    <div v-if="reservations.links.length > 3" class="p-4 border-t border-[#1E232D] flex justify-center gap-1">
                        <template v-for="(link, i) in reservations.links" :key="i">
                            <span v-if="link.url" v-html="link.label"
                                :class="['px-3 py-1 text-sm rounded border', link.active ? 'bg-neon-green-500 text-black border-neon-green-500 font-bold' : 'border-[#2A303C] hover:bg-[#1E232D] text-gray-400 cursor-pointer transition-colors']"
                                @click="link.active ? null : router.get(link.url)"
                            />
                            <span v-else v-html="link.label" class="px-3 py-1 text-sm rounded border border-[#1E232D] text-gray-600" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
