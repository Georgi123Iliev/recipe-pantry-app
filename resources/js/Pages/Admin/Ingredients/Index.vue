<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    ingredients: Array,
    categories: Array,
    errors: Object,
});

const isEditing = ref(false);
const showForm = ref(false);

const form = useForm({
    id: null,
    name: '',
    base_unit: 'g',
    ingredient_category_id: '',
});

const openCreateForm = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showForm.value = true;
};

const openEditForm = (ingredient) => {
    isEditing.value = true;
    form.id = ingredient.id;
    form.name = ingredient.name;
    form.base_unit = ingredient.base_unit;
    form.ingredient_category_id = ingredient.ingredient_category_id;
    form.clearErrors();
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditing.value) {
        form.patch(route('admin.ingredients.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    } else {
        form.post(route('admin.ingredients.store'), {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    }
};

const deleteIngredient = (ingredient) => {
    if (confirm(`Сигурни ли сте, че искате да изтриете съставката "${ingredient.name}"?`)) {
        router.delete(route('admin.ingredients.destroy', ingredient.id), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Речник на съставките" />

    <AuthenticatedLayout>
        <div class="notebook-page relative">
            <div class="flex justify-between items-center mb-6">
                <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                    Речник на съставките
                </h1>
                <div class="flex gap-4 items-center">
                    <Link :href="route('admin.users.index')" class="text-text-muted hover:underline font-montserrat font-semibold">
                        &larr; Към потребители
                    </Link>
                    <button @click="openCreateForm" class="btn-add">
                        + Нова Съставка
                    </button>
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
                            <th class="py-3 px-4 text-text-dark font-semibold">Категория</th>
                            <th class="py-3 px-4 text-text-dark font-semibold">Основна мерна единица</th>
                            <th class="py-3 px-4 text-text-dark font-semibold text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ingredient in ingredients" :key="ingredient.id" class="border-b border-text-muted/20 hover:bg-bg-beige/30 transition">
                            <td class="py-3 px-4 text-text-dark">{{ ingredient.name }}</td>
                            <td class="py-3 px-4 text-text-dark">{{ ingredient.category?.name }}</td>
                            <td class="py-3 px-4 text-text-dark">{{ ingredient.base_unit }}</td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button
                                    @click="openEditForm(ingredient)"
                                    class="text-xs px-3 py-1 border border-text-muted text-text-dark rounded-md hover:bg-text-muted/20 transition"
                                >
                                    Редактирай
                                </button>
                                <button
                                    @click="deleteIngredient(ingredient)"
                                    class="text-xs px-3 py-1 border border-cherry-red bg-cherry-red text-white rounded-md hover:bg-red-700 transition"
                                >
                                    Изтрий
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Form Modal -->
            <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-paper-white p-8 rounded-lg shadow-xl w-full max-w-md relative">
                    <button @click="closeForm" class="absolute top-4 right-4 text-text-muted hover:text-text-dark text-xl leading-none">&times;</button>
                    
                    <h2 class="font-caveat text-3xl text-text-dark mb-6">
                        {{ isEditing ? 'Редактирай съставка' : 'Нова съставка' }}
                    </h2>
                    
                    <form @submit.prevent="submitForm" class="space-y-4 font-montserrat">
                        <div>
                            <label class="block text-sm font-semibold text-text-dark mb-1">Име на съставката</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-cherry-red/50 focus:border-cherry-red outline-none transition"
                                :class="{ 'border-cherry-red': form.errors.name, 'border-text-muted': !form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-cherry-red">{{ form.errors.name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-text-dark mb-1">Категория</label>
                            <select 
                                v-model="form.ingredient_category_id" 
                                class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-cherry-red/50 focus:border-cherry-red outline-none transition"
                                :class="{ 'border-cherry-red': form.errors.ingredient_category_id, 'border-text-muted': !form.errors.ingredient_category_id }"
                            >
                                <option value="" disabled>Избери категория...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <p v-if="form.errors.ingredient_category_id" class="mt-1 text-xs text-cherry-red">{{ form.errors.ingredient_category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-text-dark mb-1">Основна мерна единица</label>
                            <select 
                                v-model="form.base_unit" 
                                class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-cherry-red/50 focus:border-cherry-red outline-none transition"
                                :class="{ 'border-cherry-red': form.errors.base_unit, 'border-text-muted': !form.errors.base_unit }"
                            >
                                <option value="g">Грамове (g)</option>
                                <option value="ml">Милилитри (ml)</option>
                                <option value="бр.">Брой (бр.)</option>
                            </select>
                            <p v-if="form.errors.base_unit" class="mt-1 text-xs text-cherry-red">{{ form.errors.base_unit }}</p>
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="closeForm" class="px-4 py-2 border border-text-muted text-text-dark rounded-md hover:bg-gray-100 transition">
                                Отказ
                            </button>
                            <button type="submit" :disabled="form.processing" class="btn-add">
                                Запази
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
