<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center gingham-bg pt-6 sm:justify-center sm:pt-0"
    >
        <div class="mb-6">
            <Link href="/">
                <h1 class="font-caveat text-cherry-red text-5xl m-0" style="transform: rotate(-2deg);">
                    Рецептите на Мама
                </h1>
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md notebook-page"
        >
            <slot />
        </div>
    </div>
</template>
