<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    rows: {
        type: Number,
        default: 10,
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);


</script>

<template>
    <textarea
        ref="input"
        :rows="rows"
        class="w-full textarea textarea-bordered textarea-secondary"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    </textarea>
</template>
