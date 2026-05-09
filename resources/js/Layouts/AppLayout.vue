<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AnimatedBackground from '@/Components/AnimatedBackground.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const page = usePage();
const showingNavigationDashboard = ref(false);

const isAdmin = computed(() => page.props.auth?.user?.is_admin);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <AnimatedBackground />

        <div class="min-h-screen bg-[#0B0E14] relative text-gray-300">
            <nav class="bg-[#0F1219] border-b border-[#1E232D] shadow-[0_0_20px_rgba(34,197,94,0.15)] relative z-50 overflow-hidden">
            <!-- Efecto de línea neón en el nav -->
            <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/50 to-transparent"></div>
            <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/30 to-transparent"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo />
                            </Link>
                        </div>

                            <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="text-gray-300 hover:text-white transition-colors">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('spaces.index')" :active="route().current('spaces.index') || route().current('spaces.show')" class="text-gray-300 hover:text-white transition-colors">
                                Espacios
                            </NavLink>
                            <NavLink :href="route('my-reservations')" :active="route().current('my-reservations')" class="text-gray-300 hover:text-white transition-colors">
                                Mis Reservas
                            </NavLink>
                            <NavLink v-if="isAdmin" :href="route('admin.home')" :active="route().current('admin.*')" class="!text-neon-green-500 hover:!text-neon-green-400 font-semibold transition-colors">
                                Admin
                            </NavLink>
                        </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <span v-if="isAdmin" class="inline-flex items-center px-3 py-1 text-xs font-bold text-neon-green-500 bg-neon-green-500/10 border border-neon-green-500/30 rounded-full">
                                <svg class="size-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 0l-3.75 0m5.25 0c.414 0 .75.336.75.75l0 0c0 .414-.336.75-.75.75H9.75c-.414 0-.75-.336-.75-.75l0 0c0-.414.336-.75.75-.75H15m0 0v4.5c0 .414.336.75.75.75h.75c.414 0 .75-.336.75-.75V9.75c0-.414-.336-.75-.75-.75H15m0 0H3.75" />
                                </svg>
                                Admin
                            </span>
                            <span v-else class="inline-flex items-center px-3 py-1 text-xs font-bold text-blue-400 bg-blue-500/10 border border-blue-500/30 rounded-full">
                                <svg class="size-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Usuario
                            </span>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#1E232D] focus:outline-none focus:bg-[#1E232D] focus:text-white transition duration-150 ease-in-out" @click="showingNavigationDashboard = ! showingNavigationDashboard">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDashboard, 'inline-flex': ! showingNavigationDashboard }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDashboard, 'inline-flex': showingNavigationDashboard }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDashboard, 'hidden': ! showingNavigationDashboard}" class="sm:hidden bg-[#0F1219] border-t border-[#1E232D]">
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('spaces.index')" :active="route().current('spaces.index') || route().current('spaces.show')">
                                Espacios
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('my-reservations')" :active="route().current('my-reservations')">
                                Mis Reservas
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="isAdmin" :href="route('admin.home')" :active="route().current('admin.*')" class="!text-neon-green-500 font-semibold hover:bg-[#1E232D]">
                                Admin
                            </ResponsiveNavLink>
                        </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-[#1E232D]">
                        <div class="flex items-center px-4">
                            <div>
                                <div class="font-medium text-base text-white">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-400">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-[#0F1219] shadow-sm border-b border-[#1E232D]">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="relative z-10">
                <slot />
            </main>

            <!-- Botón Flotante Admin - Inferior Derecha -->
            <div class="fixed bottom-6 right-6 z-50">
                <div class="relative">
                    <button
                        @click="showingNavigationDashboard = !showingNavigationDashboard"
                        class="w-14 h-14 rounded-full bg-[#0F1219] border-2 border-neon-green-500/50 shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:shadow-[0_0_30px_rgba(34,197,94,0.6)] transition-all duration-300 flex items-center justify-center group"
                    >
                        <span class="text-neon-green-500 font-bold text-lg">{{ $page.props.auth.user.name.charAt(0).toUpperCase() }}</span>
                        <div class="absolute inset-0 rounded-full bg-neon-green-500/20 animate-ping"></div>
                    </button>

                    <!-- Menú Desplegable Simplificado -->
                    <div
                        v-show="showingNavigationDashboard"
                        class="absolute bottom-16 right-0 w-48 bg-[#0F1219] rounded-xl shadow-[0_0_40px_rgba(0,0,0,0.5)] border border-[#1E232D] overflow-hidden"
                    >
                        <div class="px-4 py-3 border-b border-[#1E232D]">
                            <p class="text-sm font-medium text-white">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-gray-400">{{ $page.props.auth.user.email }}</p>
                        </div>
                        <div class="border-t border-[#1E232D]">
                            <form @submit.prevent="logout">
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
