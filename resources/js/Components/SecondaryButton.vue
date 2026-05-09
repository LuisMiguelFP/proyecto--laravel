<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-[#1E232D] border border-[#2A303C] rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest shadow-sm hover:bg-[#2A303C] hover:text-white focus:outline-none focus:ring-2 focus:ring-neon-green-500 focus:ring-offset-2 focus:ring-offset-[#0B0E14] disabled:opacity-25 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
