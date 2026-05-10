<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    perfect: Object,
    partial: Object,
});
</script>

<template>
    <Head title="Предложения" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Какво да сготвя?
            </h1>
            <p class="font-caveat text-text-muted" style="font-size: 1.5rem;">
                Тази страница ще бъде попълнена в следващата фаза.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
