<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация" />

        <form @submit.prevent="submit" class="relative z-10">
            <h2 class="font-lora text-2xl mb-6 text-text-dark border-b-2 border-dashed border-text-muted pb-2">
                Създай своя тетрадка
            </h2>

            <div>
                <InputLabel for="name" value="Име" class="font-montserrat uppercase text-xs tracking-widest text-text-muted" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-text-muted focus:border-cherry-red focus:ring-cherry-red"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Имейл" class="font-montserrat uppercase text-xs tracking-widest text-text-muted" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-text-muted focus:border-cherry-red focus:ring-cherry-red"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Потвърди парола"
                    class="font-montserrat uppercase text-xs tracking-widest text-text-muted"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full border-text-muted focus:border-cherry-red focus:ring-cherry-red"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-8 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-text-muted hover:text-text-dark font-lora italic no-underline border-b border-transparent hover:border-text-muted transition-all"
                >
                    Вече имате акаунт?
                </Link>

                <button
                    type="submit"
                    class="btn-add"
                    style="background-color: #C24641; box-shadow: 2px 2px 0px #4E342E;"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Регистрация
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
