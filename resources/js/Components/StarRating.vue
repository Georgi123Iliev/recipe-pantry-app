<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

// Unique ID per component instance to avoid SVG clipPath conflicts
const uid = ref('');
onMounted(() => {
    uid.value = 'sr-' + Math.random().toString(36).substring(2, 9);
});

const hoveredValue = ref(0);

const activeValue = computed(() => {
    if (!props.readonly && hoveredValue.value > 0) {
        return hoveredValue.value;
    }
    return props.modelValue;
});

const onHalfClick = (star) => {
    if (props.readonly) return;
    const val = star - 0.5;
    emit('update:modelValue', val);
};

const onFullClick = (star) => {
    if (props.readonly) return;
    emit('update:modelValue', star);
};

const onHalfHover = (star) => {
    if (props.readonly) return;
    hoveredValue.value = star - 0.5;
};

const onFullHover = (star) => {
    if (props.readonly) return;
    hoveredValue.value = star;
};

const onMouseLeave = () => {
    hoveredValue.value = 0;
};

const starFill = (star) => {
    if (activeValue.value >= star) return 'full';
    if (activeValue.value >= star - 0.5) return 'half';
    return 'empty';
};
</script>

<template>
    <div
        class="star-rating"
        :class="{ 'star-rating--interactive': !readonly }"
        @mouseleave="onMouseLeave"
    >
        <div
            v-for="star in 5"
            :key="star"
            class="star-wrapper"
        >
            <svg
                class="star-icon"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
            >
                <defs>
                    <clipPath :id="uid + '-left-' + star">
                        <rect x="0" y="0" width="12" height="24" />
                    </clipPath>
                    <clipPath :id="uid + '-right-' + star">
                        <rect x="12" y="0" width="12" height="24" />
                    </clipPath>
                </defs>

                <!-- Full empty star background -->
                <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"
                    fill="#d4c5b9"
                />

                <!-- Left half fill (golden if half or full) -->
                <path
                    v-if="starFill(star) === 'half' || starFill(star) === 'full'"
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"
                    fill="#DAA520"
                    :clip-path="'url(#' + uid + '-left-' + star + ')'"
                />

                <!-- Right half fill (golden only if full) -->
                <path
                    v-if="starFill(star) === 'full'"
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"
                    fill="#DAA520"
                    :clip-path="'url(#' + uid + '-right-' + star + ')'"
                />
            </svg>

            <!-- Invisible click/hover zones (left half + right half) -->
            <div
                v-if="!readonly"
                class="hit-zone hit-zone--left"
                @click="onHalfClick(star)"
                @mouseenter="onHalfHover(star)"
            />
            <div
                v-if="!readonly"
                class="hit-zone hit-zone--right"
                @click="onFullClick(star)"
                @mouseenter="onFullHover(star)"
            />
        </div>
    </div>
</template>

<style scoped>
.star-rating {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.star-wrapper {
    position: relative;
    width: 28px;
    height: 28px;
}

.star-icon {
    width: 28px;
    height: 28px;
    display: block;
    transition: transform 0.15s ease;
    pointer-events: none;
}

/* Invisible left/right halves that capture clicks */
.hit-zone {
    position: absolute;
    top: 0;
    width: 50%;
    height: 100%;
    z-index: 2;
}

.hit-zone--left {
    left: 0;
}

.hit-zone--right {
    right: 0;
}

.star-rating--interactive .star-wrapper {
    cursor: pointer;
}

.star-rating--interactive .star-wrapper:hover .star-icon {
    transform: scale(1.15);
}
</style>
