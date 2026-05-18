<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    feature: {
        type: String,
        default: '',
    },
});

const featureMessages = {
    pantry: {
        title: 'Моят Килер',
        description: 'Килерът ти позволява да следиш какви продукти имаш вкъщи и да откриваш рецепти, които можеш да приготвиш с наличните съставки.',
        icon: '🏪',
    },
    recipe: {
        title: 'Нова Рецепта',
        description: 'Сподели любимата си рецепта с общността! Добави съставки, стъпки за приготвяне и снимки.',
        icon: '📝',
    },
    rate: {
        title: 'Оценяване',
        description: 'Оцени рецептите и сподели мнението си, за да помогнеш на другите готвачи.',
        icon: '⭐',
    },
};
</script>

<template>
    <Head title="Необходим е акаунт" />

    <AuthenticatedLayout>
        <div class="notebook-page" style="max-width: 650px; margin: 0 auto;">
            <div class="notebook-section" style="text-align: center; padding: 3rem 2rem;">
                <!-- Icon -->
                <div style="font-size: 4rem; margin-bottom: 2rem; line-height: 1;">
                    {{ featureMessages[feature]?.icon || '🔒' }}
                </div>

                <!-- Title -->
                <h1 class="font-lora text-text-dark" style="font-size: 2rem; margin-bottom: 0.5rem;">
                    {{ featureMessages[feature]?.title || 'Тази функция' }}
                </h1>

                <!-- Description -->
                <p class="font-lora text-text-muted" style="font-size: 1.1rem; line-height: 1.8; margin: 1rem auto; max-width: 450px;">
                    {{ featureMessages[feature]?.description || 'Тази функционалност изисква регистрация.' }}
                </p>

                <!-- Divider -->
                <div style="border-top: 2px dashed #e1daca; margin: 2rem auto; max-width: 300px;"></div>

                <!-- Message -->
                <p class="font-caveat text-text-dark" style="font-size: 1.6rem; margin-bottom: 2rem;">
                    Създай безплатен акаунт, за да започнеш!
                </p>

                <!-- Action buttons -->
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    <Link
                        :href="route('register')"
                        class="btn-add"
                        style="font-size: 1rem; padding: 0.7rem 2rem;"
                    >
                        Регистрация
                    </Link>
                    <Link
                        :href="route('login')"
                        class="btn-add"
                        style="font-size: 1rem; padding: 0.7rem 2rem; background-color: #8D6E63; box-shadow: 2px 2px 0px #4E342E;"
                    >
                        Вход
                    </Link>
                </div>

                <!-- Back link -->
                <div class="mt-8">
                    <Link :href="route('recipes.index')" class="font-montserrat text-sm text-text-muted hover:text-cherry-red no-underline">
                        ← Обратно към каталога
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
