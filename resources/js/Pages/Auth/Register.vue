<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onError: () => {
            const msg = form.errors.email || form.errors.password || form.errors.name || 'No se pudo completar el registro';
            Swal.fire({
                title: 'Error',
                text: msg,
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
    <Head title="Crear cuenta" />
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre completo" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Registrarme
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t border-[#1E232D] text-center">
            <p class="text-sm text-gray-400 mb-3">¿Ya tienes una cuenta?</p>
            <Link
                :href="route('login')"
                class="w-full inline-block text-center px-4 py-2 border-2 border-neon-green-500/30 text-neon-green-500 font-semibold rounded-lg hover:bg-neon-green-500/10 transition text-sm"
            >
                Iniciar sesión
            </Link>
        </div>
    </AuthenticationCard>
</template>
