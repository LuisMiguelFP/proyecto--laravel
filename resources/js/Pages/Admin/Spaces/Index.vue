<script setup>
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    spaces: Array,
});

function toggleActive(space) {
    const action = !space.is_active ? 'activar' : 'desactivar';
    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} espacio?`,
        text: `El espacio "${space.name}" será ${action}do`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/admin/spaces/${space.slug}`, {
                name: space.name,
                type: space.type,
                capacity: space.capacity,
                is_active: !space.is_active,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: `${action.charAt(0).toUpperCase() + action.slice(1)}do`,
                        text: `El espacio ha sido ${action}do`,
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

function remove(space) {
    Swal.fire({
        title: '¿Eliminar espacio?',
        text: `El espacio "${space.name}" será eliminado permanentemente`,
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
            router.delete(`/admin/spaces/${space.slug}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'El espacio ha sido eliminado',
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
    <AppLayout title="Espacios — Admin">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-white leading-tight">Gestión de Espacios</h2>
                <button
                    @click="router.get('/admin/spaces/create')"
                    class="px-4 py-2 bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-bold rounded-lg text-sm transition shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-[0_0_20px_rgba(34,197,94,0.5)]"
                >
                    + Nuevo Espacio
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="spaces.length" class="grid gap-4">
                    <div
                        v-for="space in spaces"
                        :key="space.id"
                        class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-[#1E232D]"
                    >
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-lg font-bold text-white">{{ space.name }}</h3>
                                <span :class="['text-xs px-2 py-0.5 rounded-full font-medium border', space.is_active ? 'bg-neon-green-500/10 text-neon-green-400 border-neon-green-500/30' : 'bg-gray-500/10 text-gray-400 border-gray-500/30']">
                                    {{ space.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400">{{ space.type }} · Capacidad: {{ space.capacity }} · {{ space.price_per_hour ? '$' + Number(space.price_per_hour).toLocaleString() + '/h' : 'Gratuito' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ space.reservations_count }} reservas</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="router.get(`/admin/spaces/${space.slug}/edit`)"
                                class="px-3 py-1.5 text-sm text-white border border-[#2A303C] rounded-lg hover:bg-[#1E232D] transition-colors"
                            >
                                Editar
                            </button>
                            <button
                                @click="toggleActive(space)"
                                class="px-3 py-1.5 text-sm border rounded-lg transition-colors"
                                :class="space.is_active ? 'text-yellow-400 border-yellow-500/30 hover:bg-yellow-500/10' : 'text-neon-green-400 border-neon-green-500/30 hover:bg-neon-green-500/10'"
                            >
                                {{ space.is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                            <button
                                @click="remove(space)"
                                class="px-3 py-1.5 text-sm text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/10 transition-colors"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D] p-12 text-center text-gray-500">
                    No hay espacios creados.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
