<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reservations: {
        type: Array,
        default: () => [],
    },
    spaces: {
        type: Array,
        default: () => [],
    },
    levels: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const showForm = ref(false);
const loading = ref(false);

const form = ref({
    space_id: '',
    level: 'basica',
    start_time: '',
    end_time: '',
    notes: '',
});

const formErrors = ref({});

const statusColors = {
    pendiente: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    confirmada: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    rechazada: 'bg-red-500/20 text-red-400 border border-red-500/30',
    cancelada: 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    finalizada: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
};

const levelColors = {
    basica: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    estandar: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    gold: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
};

const myReservations = computed(() => {
    return props.reservations;
});

const stats = computed(() => ({
    total: props.reservations.length,
    pending: props.reservations.filter(r => r.status === 'pendiente').length,
    confirmed: props.reservations.filter(r => r.status === 'confirmada').length,
    cancelled: props.reservations.filter(r => ['rechazada', 'cancelada'].includes(r.status)).length,
}));

function resetForm() {
    form.value = { space_id: '', level: 'basica', start_time: '', end_time: '', notes: '' };
    formErrors.value = {};
    showForm.value = false;
}

async function submitReservation() {
    const spaceName = props.spaces.find(s => s.id == form.value.space_id)?.name || '—'
    const levelLabel = props.levels[form.value.level]?.label || form.value.level

    const confirmed = await Swal.fire({
        title: '¿Crear reserva?',
        html: `
            <div style="text-align: left; color: #d1d5db; line-height: 1.8;">
                <p><strong style="color: #fff;">Espacio:</strong> ${spaceName}</p>
                <p><strong style="color: #fff;">Nivel:</strong> ${levelLabel}</p>
                <p><strong style="color: #fff;">Inicio:</strong> ${form.value.start_time}</p>
                <p><strong style="color: #fff;">Fin:</strong> ${form.value.end_time}</p>
                <p><strong style="color: #fff;">Notas:</strong> ${form.value.notes || '—'}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, crear reserva',
        cancelButtonText: 'Cancelar',
        background: '#0F1219',
        color: '#d1d5db',
    });

    if (!confirmed.isConfirmed) return;

    loading.value = true;
    formErrors.value = {};

    router.post('/reservations', {
        ...form.value,
        user_name: page.props.auth.user.name,
        user_email: page.props.auth.user.email,
    }, {
        preserveScroll: true,
        onError: (errors) => {
            formErrors.value = errors;
            loading.value = false;
            if (errors.availability) {
                Swal.fire({
                    title: 'Horario no disponible',
                    text: errors.availability,
                    icon: 'warning',
                    confirmButtonColor: '#22c55e',
                    background: '#0F1219',
                    color: '#d1d5db'
                });
            }
        },
        onSuccess: () => {
            // Inertia navegará a la página de Confirmación
            // que ya tiene su propio Swal automático
        },
    });
}

function cancelReservation(slug) {
    Swal.fire({
        title: '¿Cancelar reserva?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, volver',
        background: '#0F1219',
        color: '#d1d5db'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/my-reservations/${slug}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Cancelada',
                        text: 'La reserva ha sido cancelada',
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

function formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price);
}
</script>

<template>
    <AppLayout title="Mi Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-white leading-tight">Mi Dashboard</h2>
                    <p class="text-sm text-gray-400 mt-1">Bienvenido de vuelta, {{ page.props.auth.user.name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        v-if="page.props.auth.user.is_admin"
                        @click="router.get('/admin/home')"
                        class="px-4 py-2 bg-[#1E232D] hover:bg-[#2A303C] text-white font-semibold rounded-lg text-sm transition shadow-md hover:shadow-lg border border-[#2A303C]"
                    >
                        Panel Admin
                    </button>
                    <button
                        @click="showForm = !showForm"
                        class="px-4 py-2 bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-bold rounded-lg text-sm transition shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-[0_0_20px_rgba(34,197,94,0.5)]"
                    >
                        + Nueva Reserva
                    </button>
                    <button
                        @click="router.get('/spaces')"
                        class="px-4 py-2 border border-[#2A303C] hover:bg-[#1E232D] text-gray-300 font-semibold rounded-lg text-sm transition"
                    >
                        Ver Espacios
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.5)] p-5 border-l-4 border-l-neon-green-500 border-y border-r border-[#1E232D] hover:shadow-[0_4px_25px_rgba(34,197,94,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(34,197,94,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-2 rounded-full bg-neon-green-500 animate-pulse"></div>
                            <p class="text-sm text-gray-400">Total reservas</p>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ stats.total }}</p>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/50 to-transparent"></div>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.5)] p-5 border-l-4 border-l-yellow-500 border-y border-r border-[#1E232D] hover:shadow-[0_4px_25px_rgba(234,179,8,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(234,179,8,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-2 rounded-full bg-yellow-500 animate-pulse"></div>
                            <p class="text-sm text-gray-400">Pendientes</p>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ stats.pending }}</p>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-yellow-500/50 to-transparent"></div>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.5)] p-5 border-l-4 border-l-neon-green-400 border-y border-r border-[#1E232D] hover:shadow-[0_4px_25px_rgba(74,222,128,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(74,222,128,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-2 rounded-full bg-neon-green-400 animate-pulse"></div>
                            <p class="text-sm text-gray-400">Confirmadas</p>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ stats.confirmed }}</p>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-400/50 to-transparent"></div>
                    </div>
                    <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.5)] p-5 border-l-4 border-l-red-500 border-y border-r border-[#1E232D] hover:shadow-[0_4px_25px_rgba(239,68,68,0.2)] transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(239,68,68,0.05),transparent] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-2 rounded-full bg-red-500 animate-pulse"></div>
                            <p class="text-sm text-gray-400">Canceladas/Rechazadas</p>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ stats.cancelled }}</p>
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-red-500/50 to-transparent"></div>
                    </div>
                </div>

                <!-- New Reservation Form -->
                <div v-if="showForm" class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 mb-8 border border-[#1E232D]">
                    <h3 class="text-lg font-bold text-white mb-4">Nueva Reserva</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Espacio</label>
                            <select v-model="form.space_id" class="w-full border-[#2A303C] bg-[#1E232D] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none text-white">
                                <option value="" class="text-gray-400">Seleccionar...</option>
                                <option v-for="s in spaces" :key="s.id" :value="s.id" class="text-white">{{ s.name }}</option>
                            </select>
                            <p v-if="formErrors.space_id" class="text-red-400 text-xs mt-1">{{ formErrors.space_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nivel</label>
                            <select v-model="form.level" class="w-full border-[#2A303C] bg-[#1E232D] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none text-white">
                                <option value="basica" class="text-white">Básica</option>
                                <option value="estandar" class="text-white">Estándar</option>
                                <option value="gold" class="text-white">Gold</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Inicio</label>
                            <input v-model="form.start_time" type="datetime-local" class="w-full border-[#2A303C] bg-[#1E232D] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none text-white" />
                            <p v-if="formErrors.start_time" class="text-red-400 text-xs mt-1">{{ formErrors.start_time }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Fin</label>
                            <input v-model="form.end_time" type="datetime-local" class="w-full border-[#2A303C] bg-[#1E232D] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none text-white" />
                            <p v-if="formErrors.end_time" class="text-red-400 text-xs mt-1">{{ formErrors.end_time }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Notas (opcional)</label>
                            <textarea v-model="form.notes" rows="2" class="w-full border-[#2A303C] bg-[#1E232D] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none resize-none text-white" />
                        </div>
                    </div>
                    <div v-if="formErrors.availability" class="mt-3 p-2 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg text-sm">
                        {{ formErrors.availability }}
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button @click="submitReservation" :disabled="loading || !form.space_id || !form.start_time || !form.end_time" class="px-6 py-2 bg-neon-green-500 hover:bg-neon-green-400 disabled:opacity-50 disabled:cursor-not-allowed text-[#0B0E14] font-bold rounded-lg transition shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                            {{ loading ? 'Creando...' : 'Crear Reserva' }}
                        </button>
                        <button @click="resetForm" class="px-4 py-2 border border-[#2A303C] text-gray-300 rounded-lg text-sm hover:bg-[#1E232D] transition">Cancelar</button>
                    </div>
                </div>

                <!-- My Reservations List -->
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] border border-[#1E232D]">
                    <div class="p-6 border-b border-[#1E232D]">
                        <h3 class="text-lg font-bold text-white">Mis Reservas</h3>
                    </div>
                    <div v-if="myReservations.length" class="divide-y divide-[#1E232D]">
                        <div v-for="r in myReservations" :key="r.slug" class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-white">{{ r.space_name }}</span>
                                    <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', levelColors[r.level]]">{{ r.level_label }}</span>
                                    <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusColors[r.status]]">{{ r.status }}</span>
                                </div>
                                <p class="text-sm text-gray-400">{{ r.start_time }} — {{ r.end_time }}</p>
                                <p v-if="r.notes" class="text-xs text-gray-500">{{ r.notes }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-lg font-bold text-neon-green-500">{{ formatPrice(r.total_price) }}</p>
                                <button
                                    v-if="['pendiente', 'confirmada'].includes(r.status)"
                                    @click="cancelReservation(r.slug)"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/10 transition"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center text-gray-500">
                        <p class="text-lg font-medium">No tienes reservas aún</p>
                        <button @click="router.get('/spaces')" class="mt-3 inline-block text-sm text-neon-green-500 hover:text-neon-green-400 transition-colors">Reservar ahora</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
