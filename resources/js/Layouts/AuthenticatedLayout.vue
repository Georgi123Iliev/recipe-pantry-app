<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showingUserMenu = ref(false);
const page = usePage();
</script>

<template>
    <div 
        class="min-h-screen font-lora transition-colors duration-300" 
        :class="route().current('admin.*') ? 'bg-[#EFE5D0] admin-zone' : 'gingham-bg'"
        style="padding: 2rem 5%;"
    >
        <!-- Top Navigation -->
        <nav class="paper-texture flex items-center justify-between p-4 px-8 mb-8 transition-shadow"
             :class="route().current('admin.*') ? 'shadow-md border-b-4 border-cherry-red' : ''">
            <!-- Logo -->
            <Link :href="route('recipes.index')" class="block">
                <h1 class="font-caveat text-cherry-red text-4xl m-0" style="transform: rotate(-2deg);">
                    Рецептите на Мама
                </h1>
            </Link>

            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center gap-8 font-montserrat font-semibold text-sm uppercase tracking-wide">
                <Link
                    :href="route('recipes.index')"
                    class="no-underline transition-colors duration-200 hover:text-cherry-red"
                    :class="route().current('recipes.index') ? 'text-cherry-red' : 'text-text-dark'"
                >
                    Каталог
                </Link>
                <Link
                    :href="route('pantry.index')"
                    class="no-underline transition-colors duration-200 hover:text-cherry-red"
                    :class="route().current('pantry.index') ? 'text-cherry-red' : 'text-text-dark'"
                >
                    Моят Килер
                </Link>
            </div>

            <!-- Right side: Add + User -->
            <div class="hidden md:flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user.is_admin"
                    :href="route('admin.users.index')"
                    class="btn-add"
                    style="background-color: #8D6E63; box-shadow: 2px 2px 0px #4E342E;"
                >
                    Админ
                </Link>

                <Link :href="route('recipes.create')" class="btn-add">
                    + Нова Рецепта
                </Link>

                <!-- User Dropdown -->
                <div class="relative">
                    <button
                        @click="showingUserMenu = !showingUserMenu"
                        class="flex items-center gap-1 font-montserrat text-sm font-medium text-text-muted hover:text-text-dark transition-colors bg-transparent border-none cursor-pointer"
                    >
                        {{ page.props.auth.user.name }}
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div
                        v-show="showingUserMenu"
                        @click="showingUserMenu = false"
                        class="fixed inset-0 z-30"
                    ></div>
                    <div
                        v-show="showingUserMenu"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg z-40 bg-paper-white border border-gray-200"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="block px-4 py-2 text-sm text-text-dark hover:bg-bg-beige font-montserrat no-underline"
                        >
                            Профил
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left px-4 py-2 text-sm text-text-dark hover:bg-bg-beige font-montserrat border-none bg-transparent cursor-pointer"
                        >
                            Изход
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-text-muted hover:text-text-dark hover:bg-bg-beige transition bg-transparent border-none cursor-pointer"
            >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </nav>

        <!-- Mobile Navigation Dropdown -->
        <div
            v-show="showingNavigationDropdown"
            class="md:hidden paper-texture mb-4 p-4 rounded-lg"
        >
            <div class="space-y-2 font-montserrat">
                <Link
                    :href="route('recipes.index')"
                    class="block py-2 px-3 rounded text-text-dark no-underline hover:bg-bg-beige font-semibold text-sm uppercase"
                >
                    Каталог
                </Link>
                <Link
                    :href="route('pantry.index')"
                    class="block py-2 px-3 rounded text-text-dark no-underline hover:bg-bg-beige font-semibold text-sm uppercase"
                >
                    Моят Килер
                </Link>
                <Link
                    v-if="$page.props.auth.user.is_admin"
                    :href="route('admin.users.index')"
                    class="block py-2 px-3 rounded text-text-dark no-underline hover:bg-bg-beige font-semibold text-sm uppercase"
                >
                    Админ
                </Link>
                <Link
                    :href="route('recipes.create')"
                    class="block py-2 px-3 rounded text-text-dark no-underline hover:bg-bg-beige font-semibold text-sm uppercase"
                >
                    + Нова Рецепта
                </Link>
            </div>
            <div class="mt-4 pt-4 border-t border-text-muted/20">
                <div class="px-3 text-sm font-medium text-text-dark font-montserrat">
                    {{ page.props.auth.user.name }}
                </div>
                <div class="px-3 text-xs text-text-muted font-montserrat">
                    {{ page.props.auth.user.email }}
                </div>
                <div class="mt-2 space-y-1">
                    <Link
                        :href="route('profile.edit')"
                        class="block py-2 px-3 rounded text-sm text-text-dark no-underline hover:bg-bg-beige font-montserrat"
                    >
                        Профил
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block w-full text-left py-2 px-3 rounded text-sm text-text-dark hover:bg-bg-beige font-montserrat border-none bg-transparent cursor-pointer"
                    >
                        Изход
                    </Link>
                </div>
            </div>
        </div>

        <!-- Journal Container: main + sidebar grid -->
        <div class="max-w-[1100px] mx-auto grid gap-12 items-start" style="grid-template-columns: 2fr 1fr;">
            <!-- Left Column: Page Content -->
            <main>
                <slot />
            </main>

            <!-- Right Sidebar -->
            <aside class="flex flex-col gap-8 sidebar-column">
                <!-- Recipe Box / Pantry Search -->
                <div class="recipe-box">
                    <h3>Какво имаш в хладилника?</h3>
                    <p class="text-sm italic text-text-muted mb-3">
                        Въведи продуктите, а аз ще ти кажа какво да сготвим.
                    </p>
                    <input
                        type="text"
                        class="search-input"
                        placeholder="напр. яйца, брашно, сирене..."
                    />
                    <button class="btn-add w-full text-center" style="background-color: #C24641; color: white;">
                        Търси рецепта
                    </button>
                </div>

                <!-- Sticky Note Shopping List -->
                <div class="sticky-note">
                    <div class="pin"></div>
                    <h3 class="font-caveat text-2xl m-0 mb-2 text-text-dark text-center">
                        За пазаруване:
                    </h3>
                    <ul>
                        <li class="text-text-muted italic" style="font-size: 1.3rem;">
                            Списъкът е празен
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</template>

<style scoped>
@media (max-width: 900px) {
    .sidebar-column {
        display: none;
    }
    div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
