<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-neon-green-500 border border-transparent rounded-md font-bold text-xs text-[#0B0E14] uppercase tracking-widest hover:bg-neon-green-400 focus:bg-neon-green-400 active:bg-neon-green-600 focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:ring-offset-2 focus:ring-offset-[#0B0E14] disabled:opacity-50 transition ease-in-out duration-150 shadow-[0_0_12px_rgba(34,197,94,0.2)]">
        <slot />
    </button>
</template>
