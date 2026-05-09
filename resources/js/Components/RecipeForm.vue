<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    ingredients: {
        type: Array,
        required: true,
    },
    existingImages: {
        type: Array,
        default: () => [],
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
    submitLabel: {
        type: String,
        default: 'Запази рецептата',
    },
});

const emit = defineEmits(['submit']);

const filePreviews = ref([]);
const fileInput = ref(null);

const availableUnits = ['g', 'kg', 'ml', 'l', 'супена лъжица', 'чаена лъжица', 'бр.'];

const addIngredient = () => {
    props.form.ingredients.push({
        ingredient_id: '',
        display_quantity: '',
        display_unit: '',
    });
};

const removeIngredient = (index) => {
    props.form.ingredients.splice(index, 1);
};

const removeExistingImage = (imageId) => {
    const idx = props.form.keep_images.indexOf(imageId);
    if (idx !== -1) {
        props.form.keep_images.splice(idx, 1);
    }
};

const keptImages = computed(() => {
    if (!props.existingImages) return [];
    return props.existingImages.filter(img => props.form.keep_images?.includes(img.id));
});

const handleFiles = (event) => {
    const files = Array.from(event.target.files);
    props.form.images = files;

    filePreviews.value.forEach(url => URL.revokeObjectURL(url));
    filePreviews.value = files.map(file => URL.createObjectURL(file));
};

const clearFiles = () => {
    props.form.images = [];
    filePreviews.value.forEach(url => URL.revokeObjectURL(url));
    filePreviews.value = [];
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const onSubmit = () => {
    emit('submit');
};
</script>

<template>
    <form @submit.prevent="onSubmit" class="form-container">
        <!-- Title -->
        <div class="form-section">
            <label for="recipe-title" class="form-label">Заглавие на рецепта</label>
            <input
                id="recipe-title"
                v-model="form.title"
                type="text"
                class="form-input"
                placeholder="напр. Мусака по домашному"
            />
            <p v-if="form.errors.title" class="form-error">{{ form.errors.title }}</p>
        </div>

        <!-- Description -->
        <div class="form-section">
            <label for="recipe-description" class="form-label">Описание</label>
            <textarea
                id="recipe-description"
                v-model="form.description"
                class="form-input"
                rows="3"
                placeholder="Кратко описание на рецептата..."
            ></textarea>
            <p v-if="form.errors.description" class="form-error">{{ form.errors.description }}</p>
        </div>

        <!-- Instructions -->
        <div class="form-section">
            <label for="recipe-instructions" class="form-label">Начин на приготвяне</label>
            <textarea
                id="recipe-instructions"
                v-model="form.instructions"
                class="form-input"
                rows="6"
                placeholder="Стъпка по стъпка..."
            ></textarea>
            <p v-if="form.errors.instructions" class="form-error">{{ form.errors.instructions }}</p>
        </div>

        <!-- Time + Servings -->
        <div class="form-section">
            <div class="form-row-2col">
                <div>
                    <label for="recipe-prep-time" class="form-label">Подготовка (мин)</label>
                    <input
                        id="recipe-prep-time"
                        v-model.number="form.prep_time"
                        type="number"
                        min="0"
                        class="form-input"
                        placeholder="0"
                    />
                    <p v-if="form.errors.prep_time" class="form-error">{{ form.errors.prep_time }}</p>
                </div>
                <div>
                    <label for="recipe-cook-time" class="form-label">Готвене (мин)</label>
                    <input
                        id="recipe-cook-time"
                        v-model.number="form.cook_time"
                        type="number"
                        min="0"
                        class="form-input"
                        placeholder="0"
                    />
                    <p v-if="form.errors.cook_time" class="form-error">{{ form.errors.cook_time }}</p>
                </div>
            </div>
            <div style="margin-top: 1.25rem; max-width: 200px;">
                <label for="recipe-servings" class="form-label">Порции</label>
                <input
                    id="recipe-servings"
                    v-model.number="form.servings"
                    type="number"
                    min="1"
                    class="form-input"
                    placeholder="4"
                />
                <p v-if="form.errors.servings" class="form-error">{{ form.errors.servings }}</p>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="form-section">
            <label class="form-label">Съставки</label>
            <p v-if="form.errors.ingredients" class="form-error">{{ form.errors.ingredients }}</p>

            <div
                v-for="(ingredient, index) in form.ingredients"
                :key="index"
                class="ingredient-row"
            >
                <select
                    v-model="ingredient.ingredient_id"
                    class="form-input ingredient-select"
                >
                    <option value="" disabled>Избери съставка</option>
                    <option
                        v-for="item in ingredients"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>

                <input
                    v-model.number="ingredient.display_quantity"
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="form-input ingredient-qty"
                    placeholder="Кол."
                />

                <select
                    v-model="ingredient.display_unit"
                    class="form-input ingredient-unit"
                >
                    <option value="" disabled>Мярка</option>
                    <option v-for="unit in availableUnits" :key="unit" :value="unit">
                        {{ unit }}
                    </option>
                </select>

                <button
                    type="button"
                    class="btn-remove"
                    @click="removeIngredient(index)"
                    title="Премахни"
                >
                    ✕
                </button>

                <div class="ingredient-errors">
                    <p v-if="form.errors[`ingredients.${index}.ingredient_id`]" class="form-error">
                        {{ form.errors[`ingredients.${index}.ingredient_id`] }}
                    </p>
                    <p v-if="form.errors[`ingredients.${index}.display_quantity`]" class="form-error">
                        {{ form.errors[`ingredients.${index}.display_quantity`] }}
                    </p>
                    <p v-if="form.errors[`ingredients.${index}.display_unit`]" class="form-error">
                        {{ form.errors[`ingredients.${index}.display_unit`] }}
                    </p>
                </div>
            </div>

            <button type="button" class="btn-add-ingredient" @click="addIngredient">
                + Добави съставка
            </button>
        </div>

        <!-- Existing Images (edit mode) -->
        <div v-if="isEditing && existingImages.length > 0" class="form-section">
            <label class="form-label">Текущи снимки</label>
            <div class="image-grid">
                <div
                    v-for="image in existingImages"
                    :key="image.id"
                    class="image-preview-card"
                    :class="{ 'image-removed': !form.keep_images?.includes(image.id) }"
                >
                    <img :src="image.url" :alt="'Снимка'" class="preview-img" />
                    <button
                        v-if="form.keep_images?.includes(image.id)"
                        type="button"
                        class="btn-remove-image"
                        @click="removeExistingImage(image.id)"
                    >
                        Премахни
                    </button>
                    <span v-else class="image-removed-label">Ще бъде премахната</span>
                </div>
            </div>
        </div>

        <!-- New Image Uploads -->
        <div class="form-section">
            <label class="form-label">{{ isEditing ? 'Добави нови снимки' : 'Снимки' }}</label>
            <input
                ref="fileInput"
                type="file"
                multiple
                accept="image/*"
                class="form-file-input"
                @change="handleFiles"
            />
            <p v-if="form.errors.images" class="form-error">{{ form.errors.images }}</p>

            <div v-if="filePreviews.length > 0" class="image-grid mt-3">
                <div v-for="(preview, index) in filePreviews" :key="index" class="image-preview-card">
                    <img :src="preview" alt="Преглед" class="preview-img" />
                </div>
            </div>
            <button
                v-if="filePreviews.length > 0"
                type="button"
                class="btn-clear-files"
                @click="clearFiles"
            >
                Изчисти избраните
            </button>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="btn-add"
                :disabled="form.processing"
                :class="{ 'opacity-50': form.processing }"
            >
                {{ form.processing ? 'Запазване...' : submitLabel }}
            </button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-text-muted">
                    Запазено.
                </p>
            </Transition>
        </div>
    </form>
</template>

<style scoped>
.form-container {
    max-width: 640px;
}

.form-section {
    margin-bottom: 1.75rem;
    padding-bottom: 1.75rem;
    border-bottom: 1px dashed #e1daca;
}

.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.form-label {
    display: block;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    color: #4E342E;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1px solid #8D6E63;
    background-color: #FFFDF8;
    font-family: 'Lora', serif;
    font-size: 1rem;
    color: #4E342E;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}

.form-input:focus {
    border-color: #C24641;
    box-shadow: 0 0 0 2px rgba(194, 70, 65, 0.15);
}

.form-input::placeholder {
    color: #bcaaa4;
}

.form-error {
    color: #C24641;
    font-size: 0.85rem;
    margin-top: 0.3rem;
    font-family: 'Montserrat', sans-serif;
}

.form-row-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}

.ingredient-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 0.75rem;
    align-items: start;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px dotted #e1daca;
}

.ingredient-errors {
    grid-column: 1 / -1;
}

.ingredient-select {
    min-width: 0;
}

.ingredient-qty {
    min-width: 0;
}

.ingredient-unit {
    min-width: 0;
}

.btn-remove {
    background: none;
    border: 1px solid #C24641;
    color: #C24641;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
    margin-top: 6px;
}

.btn-remove:hover {
    background-color: #C24641;
    color: white;
}

.btn-add-ingredient {
    background: none;
    border: 2px dashed #8D6E63;
    color: #8D6E63;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: border-color 0.2s, color 0.2s;
}

.btn-add-ingredient:hover {
    border-color: #4E342E;
    color: #4E342E;
}

.form-file-input {
    font-family: 'Montserrat', sans-serif;
    font-size: 0.9rem;
    color: #4E342E;
}

.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 1rem;
}

.image-preview-card {
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    border: 2px solid #e1daca;
}

.image-preview-card.image-removed {
    opacity: 0.4;
    border-color: #C24641;
}

.preview-img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    display: block;
}

.btn-remove-image {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(194, 70, 65, 0.9);
    color: white;
    border: none;
    padding: 0.3rem;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
}

.image-removed-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(78, 52, 46, 0.7);
    color: white;
    padding: 0.3rem;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.7rem;
    text-align: center;
}

.btn-clear-files {
    background: none;
    border: none;
    color: #C24641;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
    padding: 0;
}

.btn-clear-files:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .form-row-2col {
        grid-template-columns: 1fr;
    }

    .ingredient-row {
        grid-template-columns: 1fr;
    }
}
</style>
