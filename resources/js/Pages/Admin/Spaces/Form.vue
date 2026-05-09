<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    space: Object,
});

const isEdit = !!props.space;

const form = useForm({
    name: props.space?.name ?? '',
    type: props.space?.type ?? '',
    capacity: props.space?.capacity ?? 10,
    description: props.space?.description ?? '',
    rules: props.space?.rules ?? '',
    price_per_hour: props.space?.price_per_hour ?? '',
    is_active: props.space?.is_active ?? true,
    availabilities: props.space?.availabilities ?? [],
});

const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

function addAvailability() {
    form.availabilities.push({ day_of_week: 1, start_time: '08:00', end_time: '17:00' });
}

function removeAvailability(index) {
    form.availabilities.splice(index, 1);
}

function submit() {
    if (isEdit) {
        form.put(`/admin/spaces/${props.space.slug}`, {
            preserveScroll: true,
            onSuccess: () => router.visit('/admin/spaces'),
        });
    } else {
        form.post('/admin/spaces', {
            onSuccess: () => router.visit('/admin/spaces'),
        });
    }
}
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Espacio' : 'Nuevo Espacio'">
        <template #header>
            <h2 class="font-bold text-xl text-white leading-tight">{{ isEdit ? 'Editar Espacio' : 'Nuevo Espacio' }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#0F1219] rounded-xl shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-6 space-y-6 border border-[#1E232D]">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nombre</label>
                        <input v-model="form.name" type="text" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none placeholder-gray-500" />
                        <p v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Type & Capacity -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Tipo</label>
                            <input v-model="form.type" type="text" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none placeholder-gray-500" />
                            <p v-if="form.errors.type" class="text-red-400 text-xs mt-1">{{ form.errors.type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Capacidad</label>
                            <input v-model.number="form.capacity" type="number" min="1" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none" />
                            <p v-if="form.errors.capacity" class="text-red-400 text-xs mt-1">{{ form.errors.capacity }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Descripción</label>
                        <textarea v-model="form.description" rows="3" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none resize-none placeholder-gray-500" />
                    </div>

                    <!-- Rules -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Reglas</label>
                        <textarea v-model="form.rules" rows="2" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none resize-none placeholder-gray-500" />
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Precio por hora (COP)</label>
                        <input v-model="form.price_per_hour" type="number" min="0" step="1000" class="w-full bg-[#1E232D] border border-[#2A303C] text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-neon-green-500 focus:border-neon-green-500 focus:outline-none" />
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.is_active"
                            :true-value="true"
                            :false-value="false"
                            type="checkbox"
                            id="is_active"
                            class="size-4 rounded bg-[#1E232D] border-[#2A303C] text-neon-green-500 focus:ring-neon-green-500 focus:ring-offset-[#0F1219]"
                        />
                        <label for="is_active" class="text-sm text-gray-300 cursor-pointer">Espacio activo</label>
                    </div>

                    <!-- Availabilities -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-300">Disponibilidad semanal</label>
                            <button type="button" @click="addAvailability" class="text-sm font-semibold text-neon-green-500 hover:text-neon-green-400 transition-colors">+ Agregar horario</button>
                        </div>
                        <div v-if="form.availabilities.length" class="space-y-2">
                            <div v-for="(avail, index) in form.availabilities" :key="index" class="flex items-center gap-3 bg-[#1E232D] p-3 rounded-lg border border-[#2A303C]">
                                <select v-model="avail.day_of_week" class="bg-[#0F1219] border border-[#2A303C] text-white rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:outline-none">
                                    <option v-for="(dayName, idx) in dayNames" :key="idx" :value="idx">{{ dayName }}</option>
                                </select>
                                <input v-model="avail.start_time" type="time" class="bg-[#0F1219] border border-[#2A303C] text-white rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:outline-none" />
                                <span class="text-gray-500 font-medium">a</span>
                                <input v-model="avail.end_time" type="time" class="bg-[#0F1219] border border-[#2A303C] text-white rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-neon-green-500 focus:outline-none" />
                                <button type="button" @click="removeAvailability(index)" class="text-red-400 hover:text-red-300 text-lg font-bold leading-none transition-colors">×</button>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500 italic">Sin horarios definidos.</p>
                        <p v-if="form.errors.availabilities" class="text-red-400 text-xs mt-1">{{ form.errors.availabilities }}</p>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3 pt-4 border-t border-[#1E232D]">
                        <button
                            type="button"
                            @click="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-neon-green-500 hover:bg-neon-green-400 disabled:opacity-50 text-[#0B0E14] font-bold rounded-lg transition-colors shadow-[0_0_15px_rgba(34,197,94,0.3)]"
                        >
                            {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear') }}
                        </button>
                        <button
                            type="button"
                            @click="router.visit('/admin/spaces')"
                            class="px-4 py-2.5 border border-[#2A303C] text-gray-300 rounded-lg text-sm hover:bg-[#1E232D] hover:text-white transition-colors"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
