<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AnimatedBackground from '@/Components/AnimatedBackground.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            Swal.fire({
                title: '¡Bienvenido!',
                text: 'Has iniciado sesión correctamente',
                icon: 'success',
                confirmButtonColor: '#22c55e',
                background: '#0F1219',
                color: '#d1d5db',
                timer: 2000,
                showConfirmButton: false
            });
        },
        onError: () => {
            Swal.fire({
                title: 'Error',
                text: 'Credenciales incorrectas',
                icon: 'error',
                confirmButtonColor: '#22c55e',
                background: '#0F1219',
                color: '#d1d5db'
            });
        }
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0B0E14] relative">
        <AnimatedBackground />
        
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-[#0F1219] shadow-[0_0_40px_rgba(34,197,94,0.1)] overflow-hidden sm:rounded-2xl border border-[#1E232D] relative z-10">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <AuthenticationCardLogo />
            </div>
            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Bienvenido de vuelta</h2>
                <p class="text-sm text-gray-400 mt-1">Inicia sesión para continuar</p>
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-neon-green-500 bg-neon-green-500/10 p-3 rounded-lg border border-neon-green-500/20">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Correo Electrónico" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tu@email.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-5">
                    <InputLabel for="password" value="Contraseña" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between mt-5">
                    <label class="flex items-center cursor-pointer">
                        <Checkbox v-model:checked="form.remember" name="remember" class="rounded bg-[#1E232D] border-[#2A303C] text-neon-green-500 focus:ring-neon-green-500 focus:ring-offset-[#0F1219]" />
                        <span class="ms-2 text-sm text-gray-400 hover:text-gray-300 transition-colors">Recordarme</span>
                    </label>

                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-gray-400 hover:text-neon-green-400 transition-colors">
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] rounded-xl font-bold text-base shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-[0_0_25px_rgba(34,197,94,0.5)] transition-all duration-300 transform active:scale-[0.98]" :class="{ 'opacity-75 cursor-not-allowed': form.processing }" :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-[#0B0E14]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="form.processing">Iniciando sesión...</span>
                        <span v-else>Iniciar Sesión</span>
                    </button>
                </div>
                
                <div class="text-center mt-8 text-sm text-gray-400">
                    ¿No tienes una cuenta?
                    <Link :href="route('register')" class="text-neon-green-500 hover:text-neon-green-400 font-semibold transition-colors ml-1">
                        Regístrate
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>