<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    latestRecipes: Object,
});

const totalTime = (recipe) => (recipe.prep_time || 0) + (recipe.cook_time || 0);
</script>

<template>
    <Head title="Добре дошли" />
    
    <div class="gingham-bg min-h-screen font-lora" style="padding: 2rem 5%;">
        <!-- Top Navigation -->
        <nav class="paper-texture flex items-center justify-between p-4 px-8 mb-8">
            <h1 class="font-caveat text-cherry-red text-4xl m-0" style="transform: rotate(-2deg);">
                Рецептите на Мама
            </h1>
            
            <div class="flex items-center gap-8 font-montserrat font-semibold text-sm uppercase tracking-wide">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="route('recipes.index')"
                        class="text-text-dark no-underline hover:text-cherry-red transition-colors"
                    >
                        Към тетрадката
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-text-dark no-underline hover:text-cherry-red transition-colors"
                    >
                        Вход
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-text-dark no-underline hover:text-cherry-red transition-colors"
                    >
                        Регистрация
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-[1100px] mx-auto grid gap-12 items-start lg:grid-cols-[2fr,1fr]">
            
            <div class="notebook-page">
                <div class="font-caveat text-xl text-text-muted text-right mb-4">
                    {{ new Date().toLocaleDateString('bg-BG', { day: 'numeric', month: 'long', weekday: 'long' }) }}
                </div>
                
                <h1 class="font-lora text-4xl mb-6 text-text-dark">Добре дошли в кухнята!</h1>
                
                <p class="text-lg leading-relaxed text-justify mb-8">
                    Тук пазим вкусовете, които ни връщат у дома. Някои рецепти са изрязани от стари вестници, 
                    други са надраскани набързо върху салфетка, но всички те носят спомена за споделени вечери 
                    и ухание на топъл хляб.
                </p>

                <div class="polaroid mx-auto w-full max-w-[400px]">
                    <div class="tape"></div>
                    <div class="aspect-[4/3] overflow-hidden bg-gray-200">
                        <img 
                            src="/images/welcome_hero.png" 
                            alt="Семеен обяд" 
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="polaroid-caption">Обядът при баба</div>
                </div>

                <div class="mt-12 text-center">
                    <Link 
                        v-if="!$page.props.auth.user"
                        :href="route('register')" 
                        class="btn-add text-xl px-8 py-4"
                        style="background-color: #C24641; box-shadow: 4px 4px 0px #4E342E;"
                    >
                        Започни своята тетрадка
                    </Link>
                    <Link 
                        v-else
                        :href="route('recipes.index')" 
                        class="btn-add text-xl px-8 py-4"
                        style="background-color: #C24641; box-shadow: 4px 4px 0px #4E342E;"
                    >
                        Отвори тетрадката
                    </Link>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="hidden lg:flex flex-col gap-8">
                <div class="recipe-box">
                    <h3 class="font-montserrat uppercase text-sm font-bold border-b-2 border-dashed border-text-muted pb-2 mb-4">
                        Разлисти тетрадката
                    </h3>
                    <div class="flex flex-col gap-4" v-if="latestRecipes?.data?.length">
                        <Link
                            v-for="recipe in latestRecipes.data"
                            :key="recipe.id"
                            :href="route('recipes.show', recipe.id)"
                            class="recipe-row"
                        >
                            <span v-if="totalTime(recipe)" class="font-caveat text-cherry-red text-xl">{{ totalTime(recipe) }} мин</span>
                            <span v-else class="font-caveat text-text-muted text-xl">—</span>
                            <span class="font-semibold">{{ recipe.title }}</span>
                        </Link>
                    </div>
                    <p v-else class="font-caveat text-text-muted text-lg text-center py-4">
                        Все още няма рецепти.
                    </p>
                    <Link
                        :href="route('recipes.index')"
                        class="block mt-4 text-center font-montserrat text-sm text-text-muted hover:text-cherry-red no-underline transition-colors"
                    >
                        Разгледай всички →
                    </Link>
                </div>

                <div class="sticky-note rotate-1">
                    <div class="pin"></div>
                    <h3 class="font-caveat text-2xl text-center mb-4">Любима мисъл:</h3>
                    <p class="font-caveat text-xl text-center italic">
                        "Любовта е основната съставка във всяка истинска гозба."
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.recipe-row {
    @apply flex items-center gap-4 py-2 border-b border-[#e1daca] hover:bg-bg-beige/30 cursor-pointer no-underline text-text-dark transition-colors duration-200;
}

.recipe-row:hover span.font-semibold {
    color: #C24641;
}
</style>
