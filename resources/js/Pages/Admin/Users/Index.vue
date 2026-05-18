<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    users: Array,
    errors: Object,
});

const toggleAdmin = (user) => {
    router.patch(route('admin.users.update', user.id), {
        is_admin: !user.is_admin,
    }, {
        preserveScroll: true,
    });
};

const deleteUser = (user) => {
    if (confirm('Сигурни ли сте, че искате да изтриете този потребител?')) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Управление на потребители" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <div class="flex justify-between items-center mb-6">
                <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                    Управление на потребители
                </h1>
                <div class="flex gap-4">
                    <Link :href="route('admin.ingredients.index')" class="text-cherry-red hover:underline font-montserrat font-semibold">
                        Речник на съставките &rarr;
                    </Link>
                </div>
            </div>

            <div v-if="errors && errors.error" class="mb-4 p-4 bg-red-100 text-cherry-red rounded-md font-montserrat font-semibold">
                {{ errors.error }}
            </div>

            <div class="notebook-section overflow-x-auto mt-6">
                <table class="w-full text-left border-collapse font-montserrat text-sm">
                    <thead>
                        <tr class="border-b-2 border-text-muted/30">
                            <th class="py-3 px-4 text-text-dark font-semibold">Име</th>
                            <th class="py-3 px-4 text-text-dark font-semibold">Имейл</th>
                            <th class="py-3 px-4 text-text-dark font-semibold">Регистрация</th>
                            <th class="py-3 px-4 text-text-dark font-semibold">Роля</th>
                            <th class="py-3 px-4 text-text-dark font-semibold text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b border-text-muted/20 hover:bg-bg-beige/30 transition">
                            <td class="py-3 px-4 text-text-dark">{{ user.name }}</td>
                            <td class="py-3 px-4 text-text-dark">{{ user.email }}</td>
                            <td class="py-3 px-4 text-text-dark">{{ new Date(user.created_at).toLocaleDateString('bg-BG') }}</td>
                            <td class="py-3 px-4">
                                <span v-if="user.is_admin" class="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-semibold">Администратор</span>
                                <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 rounded-md text-xs font-semibold">Потребител</span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button
                                    v-if="$page.props.auth.user.id !== user.id"
                                    @click="toggleAdmin(user)"
                                    class="text-xs px-3 py-1 border rounded-md transition"
                                    :class="user.is_admin ? 'border-cherry-red text-cherry-red hover:bg-cherry-red hover:text-white' : 'border-green-600 text-green-600 hover:bg-green-600 hover:text-white'"
                                >
                                    {{ user.is_admin ? 'Премахни админ' : 'Направи админ' }}
                                </button>

                                <button
                                    v-if="$page.props.auth.user.id !== user.id"
                                    @click="deleteUser(user)"
                                    class="text-xs px-3 py-1 border border-cherry-red bg-cherry-red text-white rounded-md hover:bg-red-700 transition"
                                >
                                    Изтрий
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
