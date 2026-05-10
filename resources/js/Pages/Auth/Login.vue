<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Вход" />

        <form @submit.prevent="submit" class="relative z-10">
            <h2 class="font-lora text-2xl mb-6 text-text-dark border-b-2 border-dashed border-text-muted pb-2">
                Вход в тетрадката
            </h2>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <div>
                <InputLabel for="email" value="Имейл" class="font-montserrat uppercase text-xs tracking-widest text-text-muted" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-text-muted focus:border-cherry-red focus:ring-cherry-red"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Парола" class="font-montserrat uppercase text-xs tracking-widest text-text-muted" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border-text-muted focus:border-cherry-red focus:ring-cherry-red"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-cherry-red focus:ring-cherry-red" />
                    <span class="ms-2 text-sm text-text-muted font-lora italic"
                        >Запомни ме</span
                    >
                </label>
            </div>

            <div class="mt-8 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-text-muted hover:text-text-dark font-lora italic no-underline border-b border-transparent hover:border-text-muted transition-all"
                >
                    Забравена парола?
                </Link>

                <button
                    type="submit"
                    class="btn-add"
                    style="background-color: #C24641; box-shadow: 2px 2px 0px #4E342E;"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Влез
                </button>
            </div>

            <div class="mt-8 pt-6 border-t border-dashed border-text-muted text-center">
                <p class="text-sm text-text-muted font-lora italic">
                    Нямате акаунт?
                    <Link :href="route('register')" class="text-cherry-red font-semibold hover:underline">
                        Регистрирайте се тук
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
