<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: Object,
    pendingReservations: Array,
    upcomingReservations: Array,
});

const page = usePage();
const notifCount = ref(props.stats.pending);
let pollingInterval = null;

onMounted(() => {
    pollingInterval = setInterval(async () => {
        try {
            const res = await fetch('/notifications/pending-count');
            if (!res.ok) return;
            const data = await res.json();
            
            if (data.count !== notifCount.value) {
                if (data.count > notifCount.value) {
                    notifCount.value = data.count;
                    Swal.fire({
                        title: 'Nueva reserva pendiente',
                        text: 'Hay una nueva reserva esperando aprobación',
                        icon: 'info',
                        confirmButtonColor: '#22c55e',
                        background: '#0F1219',
                        color: '#d1d5db',
                        timer: 4000,
                        showConfirmButton: false
                    }).finally(() => {
                        router.reload({ only: ['stats', 'pendingReservations', 'upcomingReservations'] });
                    });
                } else {
                    notifCount.value = data.count;
                }
            }
        } catch (e) {}
    }, 3000);
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});

function accept(id) {
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
            router.post(`/admin/reservations/${id}/accept`, {}, {
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

function reject(id) {
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
            router.post(`/admin/reservations/${id}/reject`, {}, {
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

const statusColors = {
    pendiente: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    confirmada: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    rechazada: 'bg-red-500/20 text-red-400 border border-red-500/30',
    cancelada: 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    finalizada: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
};
</script>

<template>
    <AppLayout title="Panel Admin">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-white leading-tight">Panel de Administración</h2>
                <div class="flex items-center gap-3">
                    <span class="relative inline-flex">
                        <span class="inline-flex items-center justify-center px-3 py-1 text-sm font-bold text-yellow-500 bg-yellow-500/10 border border-yellow-500/30 rounded-full shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                            Reservas pendientes: {{ notifCount }}
                        </span>
                        <span v-if="notifCount > 0" class="absolute -top-1 -right-1 flex size-3">
                            <span class="animate-ping absolute inline-flex size-full bg-red-400 rounded-full opacity-75"></span>
                            <span class="relative inline-flex size-3 bg-red-500 rounded-full"></span>
                        </span>
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 border-l-4 border-l-yellow-500 border border-[#1E232D] hover:shadow-[0_4px_25px_rgba(234,179,8,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(234,179,8,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-yellow-500/50 to-transparent"></div>
                        <p class="text-sm text-gray-400">Pendientes</p>
                        <p class="text-3xl font-bold text-white">{{ stats.pending }}</p>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 border-l-4 border-l-neon-green-400 border border-[#1E232D] hover:shadow-[0_4px_25px_rgba(74,222,128,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(74,222,128,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-400/50 to-transparent"></div>
                        <p class="text-sm text-gray-400">Confirmadas</p>
                        <p class="text-3xl font-bold text-white">{{ stats.confirmed }}</p>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 border-l-4 border-l-blue-500 border border-[#1E232D] hover:shadow-[0_4px_25px_rgba(59,130,246,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(59,130,246,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"></div>
                        <p class="text-sm text-gray-400">Próximas</p>
                        <p class="text-3xl font-bold text-white">{{ stats.upcoming }}</p>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 border-l-4 border-l-neon-green-500 border border-[#1E232D] hover:shadow-[0_4px_25px_rgba(34,197,94,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(34,197,94,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/50 to-transparent"></div>
                        <p class="text-sm text-gray-400">Espacios activos</p>
                        <p class="text-3xl font-bold text-white">{{ stats.spaces }}</p>
                    </div>
                </div>

                <!-- Pending Reservations -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] mb-8 border border-[#1E232D]">
                    <div class="p-6 border-b border-[#1E232D]">
                        <h3 class="text-lg font-bold text-white">Reservas Pendientes</h3>
                    </div>
                    <div v-if="pendingReservations.length" class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-300">
                            <thead class="bg-[#1E232D] text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Espacio</th>
                                    <th class="px-4 py-3 text-left">Usuario</th>
                                    <th class="px-4 py-3 text-left">Fecha</th>
                                    <th class="px-4 py-3 text-left">Horario</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="r in pendingReservations" :key="r.slug" class="border-b border-[#1E232D] hover:bg-[#1E232D] transition-colors">
                                    <td class="px-4 py-3 font-bold text-white">{{ r.space_name }}</td>
                                    <td class="px-4 py-3">{{ r.user_name }}<br><span class="text-xs text-gray-500">{{ r.user_email }}</span></td>
                                    <td class="px-4 py-3">{{ r.start_time }}</td>
                                    <td class="px-4 py-3">{{ r.end_time }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="accept(r.slug)" class="px-3 py-1.5 text-xs font-bold text-black bg-neon-green-500 hover:bg-neon-green-400 rounded-lg mr-1 transition shadow-[0_0_10px_rgba(34,197,94,0.2)]">
                                            Aceptar
                                        </button>
                                        <button @click="reject(r.slug)" class="px-3 py-1.5 text-xs font-bold text-white bg-red-500 hover:bg-red-400 rounded-lg transition shadow-[0_0_10px_rgba(239,68,68,0.2)]">
                                            Rechazar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500">
                        No hay reservas pendientes.
                    </div>
                </div>

                <!-- Upcoming Reservations -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D]">
                    <div class="p-6 border-b border-[#1E232D]">
                        <h3 class="text-lg font-bold text-white">Próximas Reservas</h3>
                    </div>
                    <div v-if="upcomingReservations.length" class="divide-y divide-[#1E232D]">
                        <div v-for="r in upcomingReservations" :key="r.slug" class="p-4 flex items-center justify-between hover:bg-[#1E232D]/50 transition-colors">
                            <div>
                                <p class="font-bold text-white">{{ r.space_name }} <span class="font-normal text-gray-400">— {{ r.user_name }}</span></p>
                                <p class="text-sm text-gray-500">{{ r.start_time }}</p>
                            </div>
                            <span :class="['text-xs px-2 py-1 rounded-full font-medium', statusColors[r.status]]">{{ r.status }}</span>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500">
                        No hay reservas próximas.
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
