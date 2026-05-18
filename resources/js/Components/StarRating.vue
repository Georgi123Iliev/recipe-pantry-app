<script setup>
import { ref, computed } from 'vue';

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

const hoveredStar = ref(0);

const activeFill = computed(() => {
    if (!props.readonly && hoveredStar.value > 0) {
        return hoveredStar.value;
    }
    return props.modelValue;
});

const selectStar = (star) => {
    if (!props.readonly) {
        emit('update:modelValue', star);
    }
};

const onMouseEnter = (star) => {
    if (!props.readonly) {
        hoveredStar.value = star;
    }
};

const onMouseLeave = () => {
    hoveredStar.value = 0;
};
</script>

<template>
    <div
        class="star-rating"
        :class="{ 'star-rating--interactive': !readonly }"
        @mouseleave="onMouseLeave"
    >
        <svg
            v-for="star in 5"
            :key="star"
            class="star-icon"
            :class="{
                'star-icon--filled': star <= activeFill,
                'star-icon--empty': star > activeFill,
            }"
            @click="selectStar(star)"
            @mouseenter="onMouseEnter(star)"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
        >
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z" />
        </svg>
    </div>
</template>

<style scoped>
.star-rating {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.star-icon {
    width: 26px;
    height: 26px;
    transition: color 0.15s ease, transform 0.15s ease;
}

.star-icon--filled {
    color: #DAA520;
}

.star-icon--empty {
    color: #d4c5b9;
}

.star-rating--interactive .star-icon {
    cursor: pointer;
}

.star-rating--interactive .star-icon:hover {
    transform: scale(1.15);
}
</style>
